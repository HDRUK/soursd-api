<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SsoTenant;
use App\Models\SsoTenantDomain;
use App\Http\Traits\Responses;
use App\Traits\CommonFunctions;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Keycloak\KeycloakIdentityProvider;
use App\Http\Requests\SsoTenants\GetSsoTenant;
use App\Http\Requests\SsoTenants\StoreSsoTenant;
use App\Http\Requests\SsoTenants\UpdateSsoTenant;
use App\Http\Requests\SsoTenants\LookupSsoDomain;
use App\Http\Requests\SsoTenants\RejectSsoTenant;
use App\Http\Requests\SsoTenants\ReimportSsoTenantMetadata;

/**
 * @OA\Tag(
 *     name="SsoTenant",
 *     description="API endpoints for managing enterprise SAML SSO connections"
 * )
 */
class SsoTenantController extends Controller
{
    use CommonFunctions;
    use Responses;

    /**
     * Custodians and non-delegate Organisation Admins can submit a
     * connection request and see their own submissions - actually granting
     * Keycloak trust (approve/reject/update/reimport/disable/enable) stays
     * admin-only, since that federates into the shared realm every user
     * type authenticates against, not just the submitter's own org.
     */
    private function canSubmit(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->user_group === User::GROUP_CUSTODIANS) {
            return true;
        }

        return $user->isOrganisation() && !$user->is_delegate;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/sso_tenants",
     *      operationId="ssoTenantIndex",
     *      x={"internal"="true"},
     *      summary="Return a list of SSO tenant connections",
     *      description="Admins see every connection; Custodians/Organisation Admins see only their own submissions",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/SsoTenant"
     *              )
     *          ),
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$this->canSubmit($user)) {
            return $this->ForbiddenResponse();
        }

        $ssoTenants = SsoTenant::with('domains')
            ->when(!$user->isAdmin(), fn ($query) => $query->where('submitted_by_user_id', $user->id))
            ->searchViaRequest()
            ->applySorting()
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($ssoTenants);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/sso_tenants/{id}",
     *      operationId="ssoTenantShow",
     *      x={"internal"="true"},
     *      summary="Return an SSO tenant connection by ID",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/SsoTenant"
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response"
     *      )
     * )
     */
    public function show(GetSsoTenant $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (!$this->canSubmit($user)) {
            return $this->ForbiddenResponse();
        }

        $ssoTenant = SsoTenant::with('domains')->find($id);

        if (!$ssoTenant) {
            return $this->NotFoundResponse();
        }

        if (!$user->isAdmin() && $ssoTenant->submitted_by_user_id !== $user->id) {
            return $this->ForbiddenResponse();
        }

        return $this->OKResponse($ssoTenant);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/sso_tenants",
     *      operationId="ssoTenantStore",
     *      x={"internal"="true"},
     *      summary="Submit a request to connect an enterprise SAML Identity Provider",
     *      description="Stores the request as pending. No Keycloak calls happen here - an admin must approve it first.",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *                  @OA\Property(property="name", type="string", example="Acme Corp"),
     *                  @OA\Property(property="metadata_url", type="string", example="https://sso.acme.example/federationmetadata.xml"),
     *                  @OA\Property(property="metadata_xml", type="string", example="<EntityDescriptor ...>"),
     *                  @OA\Property(property="domains", type="array", @OA\Items(type="string", example="acme.com"))
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/SsoTenant"
     *              )
     *          ),
     *      )
     * )
     */
    public function store(StoreSsoTenant $request): JsonResponse
    {
        $user = $request->user();

        if (!$this->canSubmit($user)) {
            return $this->ForbiddenResponse();
        }

        $input = $request->validated();

        $ssoTenant = DB::transaction(function () use ($input, $user) {
            $ssoTenant = SsoTenant::create([
                'name' => $input['name'],
                'metadata_url' => $input['metadata_url'] ?? null,
                'metadata_xml' => $input['metadata_xml'] ?? null,
                'status' => SsoTenant::STATUS_PENDING,
                'enabled' => false,
                'submitted_by_user_id' => $user->id,
            ]);

            foreach ($input['domains'] as $domain) {
                SsoTenantDomain::create([
                    'sso_tenant_id' => $ssoTenant->id,
                    'domain' => strtolower($domain),
                ]);
            }

            return $ssoTenant->load('domains');
        });

        return $this->CreatedResponse($ssoTenant);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/sso_tenants/{id}/approve",
     *      operationId="ssoTenantApprove",
     *      x={"internal"="true"},
     *      summary="Approve a pending SSO connection request",
     *      description="The only place Keycloak provisioning actually happens - imports the submitted metadata, creates the Identity Provider and its standard attribute mappers, and generates its alias",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function approve(GetSsoTenant $request, int $id): JsonResponse
    {
        if (!Gate::allows('admin')) {
            return $this->ForbiddenResponse();
        }

        $ssoTenant = SsoTenant::find($id);

        if (!$ssoTenant) {
            return $this->NotFoundResponse();
        }

        if ($ssoTenant->status !== SsoTenant::STATUS_PENDING) {
            return $this->ConflictResponse('This request has already been ' . $ssoTenant->status);
        }

        $alias = null;

        try {
            $parsedConfig = KeycloakIdentityProvider::importMetadata(
                $ssoTenant->metadata_url,
                $ssoTenant->metadata_xml
            );

            $alias = $this->generateUniqueAlias($ssoTenant->name);

            KeycloakIdentityProvider::createIdentityProvider($alias, $parsedConfig, $ssoTenant->name);
            KeycloakIdentityProvider::createAttributeMappers($alias);

            $ssoTenant->update([
                'idp_alias' => $alias,
                'entity_id' => $parsedConfig['entityId'] ?? null,
                'metadata_imported_at' => now(),
                'status' => SsoTenant::STATUS_APPROVED,
                'enabled' => true,
            ]);

            return $this->OKResponse($ssoTenant->fresh('domains'));
        } catch (Exception $e) {
            // The Keycloak IdP isn't transactional with the local DB write -
            // if it was created but the local persistence failed, remove it
            // rather than leaving an orphaned IdP with no local record.
            if ($alias) {
                try {
                    KeycloakIdentityProvider::deleteIdentityProvider($alias);
                } catch (Exception $cleanupException) {
                    // Swallow - the original exception below is the one that matters,
                    // and we don't want a failed cleanup to mask it.
                }
            }

            return $this->ErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/sso_tenants/{id}/reject",
     *      operationId="ssoTenantReject",
     *      x={"internal"="true"},
     *      summary="Reject a pending SSO connection request",
     *      description="No Keycloak calls - nothing was ever provisioned for a pending request",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function reject(RejectSsoTenant $request, int $id): JsonResponse
    {
        if (!Gate::allows('admin')) {
            return $this->ForbiddenResponse();
        }

        $ssoTenant = SsoTenant::find($id);

        if (!$ssoTenant) {
            return $this->NotFoundResponse();
        }

        if ($ssoTenant->status !== SsoTenant::STATUS_PENDING) {
            return $this->ConflictResponse('This request has already been ' . $ssoTenant->status);
        }

        $ssoTenant->update([
            'status' => SsoTenant::STATUS_REJECTED,
            'rejected_reason' => $request->validated('reason'),
        ]);

        return $this->OKResponse($ssoTenant->fresh());
    }

    /**
     * @OA\Put(
     *      path="/api/v1/sso_tenants/{id}",
     *      operationId="ssoTenantUpdate",
     *      x={"internal"="true"},
     *      summary="Edit an SSO tenant's name and/or domains",
     *      description="Does not touch the SAML config - see reimport for cert rotation",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/SsoTenant"
     *              )
     *          ),
     *      )
     * )
     */
    public function update(UpdateSsoTenant $request, int $id): JsonResponse
    {
        if (!Gate::allows('admin')) {
            return $this->ForbiddenResponse();
        }

        $ssoTenant = SsoTenant::find($id);

        if (!$ssoTenant) {
            return $this->NotFoundResponse();
        }

        $input = $request->validated();

        DB::transaction(function () use ($ssoTenant, $input) {
            if (isset($input['name'])) {
                $ssoTenant->update(['name' => $input['name']]);
            }

            if (isset($input['domains'])) {
                $ssoTenant->domains()->delete();

                foreach ($input['domains'] as $domain) {
                    SsoTenantDomain::create([
                        'sso_tenant_id' => $ssoTenant->id,
                        'domain' => strtolower($domain),
                    ]);
                }
            }
        });

        return $this->OKResponse($ssoTenant->fresh('domains'));
    }

    /**
     * @OA\Post(
     *      path="/api/v1/sso_tenants/{id}/reimport",
     *      operationId="ssoTenantReimportMetadata",
     *      x={"internal"="true"},
     *      summary="Re-import SAML metadata for cert rotation",
     *      description="Only valid for an already-approved tenant",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function reimportMetadata(ReimportSsoTenantMetadata $request, int $id): JsonResponse
    {
        if (!Gate::allows('admin')) {
            return $this->ForbiddenResponse();
        }

        $ssoTenant = SsoTenant::find($id);

        if (!$ssoTenant) {
            return $this->NotFoundResponse();
        }

        if ($ssoTenant->status !== SsoTenant::STATUS_APPROVED) {
            return $this->ConflictResponse('Only an approved tenant has a live Identity Provider to update');
        }

        $input = $request->validated();

        try {
            $parsedConfig = KeycloakIdentityProvider::importMetadata(
                $input['metadata_url'] ?? null,
                $input['metadata_xml'] ?? null
            );

            KeycloakIdentityProvider::updateIdentityProviderConfig($ssoTenant->idp_alias, $parsedConfig);

            $ssoTenant->update([
                'metadata_url' => $input['metadata_url'] ?? $ssoTenant->metadata_url,
                'metadata_xml' => $input['metadata_xml'] ?? $ssoTenant->metadata_xml,
                'entity_id' => $parsedConfig['entityId'] ?? $ssoTenant->entity_id,
                'metadata_imported_at' => now(),
            ]);

            return $this->OKResponse($ssoTenant->fresh('domains'));
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/sso_tenants/{id}",
     *      operationId="ssoTenantDestroy",
     *      x={"internal"="true"},
     *      summary="Disable an approved SSO tenant",
     *      description="Disables the Identity Provider in Keycloak and locally - immediate effect. Domains are left in place so re-enabling doesn't require re-entering them.",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function destroy(GetSsoTenant $request, int $id): JsonResponse
    {
        if (!Gate::allows('admin')) {
            return $this->ForbiddenResponse();
        }

        $ssoTenant = SsoTenant::find($id);

        if (!$ssoTenant) {
            return $this->NotFoundResponse();
        }

        if ($ssoTenant->status !== SsoTenant::STATUS_APPROVED) {
            return $this->ConflictResponse('Only an approved tenant has an Identity Provider to disable');
        }

        try {
            KeycloakIdentityProvider::setIdentityProviderEnabled($ssoTenant->idp_alias, false);
            $ssoTenant->update(['enabled' => false]);

            return $this->OKResponse($ssoTenant->fresh());
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/sso_tenants/{id}/purge",
     *      operationId="ssoTenantPurge",
     *      x={"internal"="true"},
     *      summary="Permanently remove an SSO tenant",
     *      description="Unlike destroy() (which only disables), this actually deletes the local record, its domains, and - if approved - the Keycloak Identity Provider itself. Irreversible; mainly useful for tearing down a test connection.",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function purge(GetSsoTenant $request, int $id): JsonResponse
    {
        if (!Gate::allows('admin')) {
            return $this->ForbiddenResponse();
        }

        $ssoTenant = SsoTenant::find($id);

        if (!$ssoTenant) {
            return $this->NotFoundResponse();
        }

        try {
            if ($ssoTenant->idp_alias) {
                KeycloakIdentityProvider::deleteIdentityProvider($ssoTenant->idp_alias);
            }

            DB::transaction(function () use ($ssoTenant) {
                $ssoTenant->domains()->delete();
                $ssoTenant->delete();
            });

            return $this->OKResponse(null);
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/sso_tenants/{id}/enable",
     *      operationId="ssoTenantEnable",
     *      x={"internal"="true"},
     *      summary="Re-enable a previously disabled SSO tenant",
     *      tags={"SsoTenant"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function enable(GetSsoTenant $request, int $id): JsonResponse
    {
        if (!Gate::allows('admin')) {
            return $this->ForbiddenResponse();
        }

        $ssoTenant = SsoTenant::find($id);

        if (!$ssoTenant) {
            return $this->NotFoundResponse();
        }

        if ($ssoTenant->status !== SsoTenant::STATUS_APPROVED) {
            return $this->ConflictResponse('Only an approved tenant has an Identity Provider to enable');
        }

        try {
            KeycloakIdentityProvider::setIdentityProviderEnabled($ssoTenant->idp_alias, true);
            $ssoTenant->update(['enabled' => true]);

            return $this->OKResponse($ssoTenant->fresh('domains'));
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/sso/lookup",
     *      operationId="ssoLookupDomain",
     *      x={"internal"="true"},
     *      summary="Resolve an email's domain to an approved SSO tenant's Keycloak IdP alias",
     *      description="Unauthenticated - runs before the user has a session. Always returns 200, never 404, so the response shape can't be used to distinguish 'no tenant' from a server error.",
     *      tags={"SsoTenant"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string", example="jane@acme.com"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="matched", type="boolean"),
     *                  @OA\Property(property="idp_alias", type="string", example="acme-corp"),
     *              )
     *          ),
     *      )
     * )
     */
    public function lookupDomain(LookupSsoDomain $request): JsonResponse
    {
        $domain = strtolower(Str::afterLast($request->validated('email'), '@'));

        $match = SsoTenantDomain::where('domain', $domain)
            ->whereHas('ssoTenant', fn ($query) => $query->where('enabled', true)
                ->where('status', SsoTenant::STATUS_APPROVED))
            ->with('ssoTenant')
            ->first();

        if (!$match) {
            return $this->OKResponse(['matched' => false]);
        }

        return $this->OKResponse([
            'matched' => true,
            'idp_alias' => $match->ssoTenant->idp_alias,
        ]);
    }

    /**
     * Slugify the tenant name into a Keycloak-safe, unique, immutable alias.
     * Falls back to a numeric suffix on collision.
     */
    private function generateUniqueAlias(string $name): string
    {
        $base = Str::slug($name);
        $alias = $base;
        $suffix = 1;

        while (SsoTenant::where('idp_alias', $alias)->exists()) {
            $suffix++;
            $alias = "{$base}-{$suffix}";
        }

        return $alias;
    }
}
