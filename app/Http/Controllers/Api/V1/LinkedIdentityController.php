<?php

namespace App\Http\Controllers\Api\V1;

use Keycloak;
use Exception;
use App\Models\User;
use App\Models\UserIdentity;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Traits\Responses;
use Tests\Traits\Authorisation;

class LinkedIdentityController extends Controller
{
    use Authorisation;
    use Responses;

    /**
     * @OA\Get(
     *      path="/api/v1/linked_identities",
     *      operationId="linkedIdentityIndex",
     *      x={"internal"="true"},
     *      summary="Return the current user's linked identities and the catalog of available providers",
     *      description="Return the current user's linked identities and the catalog of available providers",
     *      tags={"LinkedIdentity"},
     *      summary="LinkedIdentity@index",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object")
     *          ),
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        if (!Feature::active('LinkedIdentitiesEnabled')) {
            return $this->NotFoundResponse();
        }

        $user = Auth::user();

        if (!$this->isResearcher($user)) {
            return $this->ForbiddenResponse();
        }

        $linked = UserIdentity::where('user_id', $user->id)->get();
        $linkedProviders = $linked->pluck('provider')->all();

        $providers = collect(config('identity_providers.providers'))
            ->map(function (array $provider) use ($linkedProviders) {
                $provider['linked'] = in_array($provider['key'], $linkedProviders, true);

                return $provider;
            })
            ->values();

        return response()->json([
            'message' => 'success',
            'data' => [
                'linked' => $linked,
                'providers' => $providers,
            ],
        ], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/linked_identities/{provider}",
     *      operationId="linkedIdentitySync",
     *      x={"internal"="true"},
     *      summary="Sync a provider Keycloak just linked via its own account-linking flow into the local cache",
     *      description="Called after Keycloak's client-initiated account-linking redirect (/realms/{realm}/broker/{provider}/link) completes for the current user. Keycloak performs the actual OAuth handshake and attaches the federated identity itself - this endpoint just reads it back via the admin API and mirrors it locally for display/claims/activity.",
     *      tags={"LinkedIdentity"},
     *      summary="LinkedIdentity@sync",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         description="Provider key",
     *         required=true,
     *         example="orcid",
     *         @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unsupported provider",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="unsupported provider")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Keycloak has no federated identity for this provider (linking did not complete)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not linked")
     *          )
     *      )
     * )
     */
    public function sync(string $provider): JsonResponse
    {
        if (!Feature::active('LinkedIdentitiesEnabled')) {
            return $this->NotFoundResponse();
        }

        $user = Auth::user();

        if (!$this->isResearcher($user)) {
            return $this->ForbiddenResponse();
        }

        if (!$this->findActiveProvider($provider)) {
            return response()->json([
                'message' => 'unsupported provider',
            ], 422);
        }

        try {
            $keycloakToken = $this->getAuthToken();
            $federatedIdentities = Keycloak::getFederatedIdentities($keycloakToken, $user->keycloak_id);

            $federatedIdentity = collect($federatedIdentities)
                ->first(fn (array $identity) => $identity['identityProvider'] === $provider);

            if (!$federatedIdentity) {
                return response()->json([
                    'message' => 'not linked',
                ], 404);
            }

            $identity = UserIdentity::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => $provider,
                ],
                [
                    'provider_user_id' => $federatedIdentity['userId'],
                    'provider_username' => $federatedIdentity['userName'] ?? null,
                    'linked_at' => now(),
                ],
            );

            return response()->json([
                'message' => 'success',
                'data' => $identity,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/linked_identities/{provider}",
     *      operationId="linkedIdentityUnlink",
     *      x={"internal"="true"},
     *      summary="Unlink an identity provider from the current user's account",
     *      description="Unlink an identity provider from the current user's account",
     *      tags={"LinkedIdentity"},
     *      summary="LinkedIdentity@unlink",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         description="Provider key",
     *         required=true,
     *         example="orcid",
     *         @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not linked",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          )
     *      )
     * )
     */
    public function unlink(string $provider): JsonResponse
    {
        if (!Feature::active('LinkedIdentitiesEnabled')) {
            return $this->NotFoundResponse();
        }

        $user = Auth::user();

        if (!$this->isResearcher($user)) {
            return $this->ForbiddenResponse();
        }

        $identity = UserIdentity::where('user_id', $user->id)->where('provider', $provider)->first();

        if (!$identity) {
            return response()->json([
                'message' => 'not found',
            ], 404);
        }

        try {
            $keycloakToken = $this->getAuthToken();
            Keycloak::removeFederatedIdentity($keycloakToken, $user->keycloak_id, $provider);

            $identity->delete();

            return response()->json([
                'message' => 'success',
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function findActiveProvider(string $provider): ?array
    {
        return collect(config('identity_providers.providers'))
            ->first(fn (array $p) => $p['key'] === $provider && $p['status'] === 'active');
    }

    private function isResearcher(User $user): bool
    {
        return $user->user_group === User::GROUP_USERS;
    }
}
