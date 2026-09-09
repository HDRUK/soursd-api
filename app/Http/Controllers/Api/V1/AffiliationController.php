<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\State;
use App\Models\Affiliation;
use Illuminate\Support\Str;
use App\Models\Organisation;
use App\Models\CustodianHasProjectUser;
use Illuminate\Http\Request;
use App\Http\Traits\Responses;
use App\Traits\CommonFunctions;
use Illuminate\Http\JsonResponse;
use App\Traits\AffiliationManager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use App\Models\CustodianHasProjectOrganisation;
use App\Http\Requests\Affiliations\DeleteAffiliation;
use App\Http\Requests\Affiliations\UpdateAffiliation;
use App\Http\Requests\Affiliations\VerificationEmail;
use App\Http\Requests\Affiliations\ResendVerificationEmail;
use App\Http\Requests\Affiliations\GetAffiliationByRegistry;
use App\Http\Requests\Affiliations\GetOrganisationAffiliation;
use App\Http\Requests\Affiliations\CreateAffiliationByRegistry;
use App\Http\Requests\Affiliations\UpdateAffiliationByRegistry;
use App\Notifications\Organisations\OrganisationUserAffiliation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AffiliationController extends Controller
{
    use CommonFunctions;
    use Responses;
    use AffiliationManager;
    use AuthorizesRequests;

    /**
     * @OA\Get(
     *      path="/api/v1/affiliations/{registryId}",
     *      operationId="affiliationsIndexByRegistryId",
     *      x={"internal"="true"},
     *      summary="Return a list of affiliations by registry id",
     *      description="Return a list of affiliations by registry id",
     *      tags={"Affiliations"},
     *      summary="Affiliations@show",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="registryId",
     *         in="path",
     *         description="Affiliations registry id",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Affiliations registry id",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(ref="#/components/schemas/Affiliation")
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function indexByRegistryId(GetAffiliationByRegistry $request, int $registryId): JsonResponse
    {
        if (!Gate::allows('viewByRegistry', [Affiliation::class, $registryId])) {
            return $this->ForbiddenResponse();
        }

        $loggedInUserId = $request->user()?->id;
        $loggedInUser = User::where('id', $loggedInUserId)->first();
        $isUserGroupOrg = (!is_null($loggedInUser) && $loggedInUser->user_group === 'ORGANISATIONS') ? true : false;

        $affiliations = Affiliation::with(
            [
                'modelState.state',
                'organisation' => function ($query) {
                    $query->select(
                        'id',
                        'organisation_name',
                        'unclaimed',
                        'lead_applicant_email'
                    );
                },
            ]
        )
            ->where(['registry_id' => $registryId])
            ->paginate((int) $this->getSystemConfig('PER_PAGE'));

        if ($isUserGroupOrg) {
            $affiliations->getCollection()->each(function ($affiliation) use ($loggedInUser) {
                if ($affiliation->organisation_id !== $loggedInUser->organisation_id) {
                    $affiliation->setAttribute('member_id', '***');
                }
            });
        }

        return response()->json([
            'message' => 'success',
            'data' => $affiliations,
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/affiliations/{registryId}/organisation/{organisationId}",
     *      operationId="affiliationsGetOrganisationAffiliation",
     *      x={"internal"="true"},
     *      summary="Return a specific organisation's affiliation by registry ID and organisation ID",
     *      description="Get a specific organisation's affiliation for a given registry",
     *      tags={"Affiliations"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="registryId",
     *          in="path",
     *          required=true,
     *          description="Registry ID",
     *          example=1,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="organisationId",
     *          in="path",
     *          required=true,
     *          description="Organisation ID",
     *          example=100,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=123),
     *                  @OA\Property(property="registry_id", type="integer", example=1),
     *                  @OA\Property(property="organisation_id", type="integer", example=100),
     *                  @OA\Property(property="model_state_id", type="integer", example=2),
     *                  @OA\Property(property="model_state", type="object",
     *                      @OA\Property(property="id", type="integer", example=2),
     *                      @OA\Property(property="state_id", type="integer", example=5),
     *                      @OA\Property(property="state", type="object",
     *                          @OA\Property(property="id", type="integer", example=5),
     *                          @OA\Property(property="name", type="string", example="Approved")
     *                      )
     *                  ),
     *                  @OA\Property(property="organisation", type="object",
     *                      @OA\Property(property="id", type="integer", example=100),
     *                      @OA\Property(property="organisation_name", type="string", example="Example Org"),
     *                      @OA\Property(property="unclaimed", type="boolean", example=false),
     *                      @OA\Property(property="lead_applicant_email", type="string", example="lead@example.org")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Affiliation not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Affiliation not found")
     *          )
     *      )
     * )
     */
    public function getOrganisationAffiliation(GetOrganisationAffiliation $request, int $registryId, int $organisationId): JsonResponse
    {
        if (!Gate::allows('viewByRegistry', [Affiliation::class, $registryId])) {
            return $this->ForbiddenResponse();
        }

        $affiliation = Affiliation::with(
            [
                'modelState.state',
                'organisation' => function ($query) {
                    $query->select(
                        'id',
                        'organisation_name',
                        'unclaimed',
                        'lead_applicant_email'
                    );
                },
            ]
        )
            ->where(
                [
                    'registry_id' => $registryId,
                    'organisation_id' => $organisationId
                ]
            )
            ->first();


        return response()->json([
            'message' => 'success',
            'data' => $affiliation,
        ], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/affiliations/{registryId}",
     *      operationId="affiliationsStoreByRegistryId",
     *      x={"internal"="true"},
     *      summary="Create an Affiliation entry",
     *      description="Create an Affiliation entry",
     *      tags={"Affiliations"},
     *      summary="Affiliations@store",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="registryId",
     *         in="path",
     *         description="Registry entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Registry entry ID",
     *         ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Affiliation definition",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Affiliation"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Affiliation"
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function storeByRegistryId(CreateAffiliationByRegistry $request, int $registryId): JsonResponse
    {
        if (!Gate::allows('createForRegistry', [Affiliation::class, $registryId])) {
            return $this->ForbiddenResponse();
        }

        try {
            $input = $request->only(app(Affiliation::class)->getFillable());
            $user =  $request->user;
            $isCurrentEmail = (strtolower($input['email'] ?? '') === strtolower($request->user()->email));

            $currentEmployer = isset($input['current_employer']) ? $input['current_employer'] : false;
            $array = [
                'organisation_id' => $input['organisation_id'],
                'member_id' => $request['member_id'],
                'relationship' => $input['relationship'],
                'from' => $input['from'],
                'to' => $input['to'],
                'department' => $input['department'],
                'role' => $input['role'],
                'email' => $input['email'],
                'ror' => $input['ror'],
                'registry_id' => $registryId,
                'current_employer' => $currentEmployer
            ];

            $organisation = Organisation::where('id', $array['organisation_id'])->first();
            if (is_null($organisation)) {
                return $this->ErrorResponse('Organisation with id ' . $array['organisation_id'] . ' not found');
            }

            $affiliation = Affiliation::create($array);

            if ($currentEmployer) {
                $verificationCode = Str::uuid()->toString();
                $affiliation->verification_code = $verificationCode;
                $affiliation->verification_sent_at = Carbon::now();
                $affiliation->verification_confirmed_at = null;
                $affiliation->is_verified = 0;
                $affiliation->save();
            }

            if ($currentEmployer && $verificationCode && !$isCurrentEmail) {
                $affiliation->setState(State::STATE_AFFILIATION_EMAIL_VERIFY);
                $this->sendEmailVerificationAffiliation($affiliation);
            } else {
                $affiliation->setState(State::STATE_AFFILIATION_PENDING);
            }

            activity('affiliation')
                ->causedBy(Auth::user())
                ->performedOn($affiliation)
                ->withProperties([
                    'id' => $affiliation->id,
                    'attributes' => $affiliation,
                ])
                ->event('created')
                ->log('created');

            return response()->json([
                'message' => 'success',
                'data' => $affiliation->id,
            ], 201);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //Hide from swagger docs
    public function resendVerificationEmail(ResendVerificationEmail $request, int $id): JsonResponse
    {
        try {
            $affiliation = Affiliation::where([
                'id' => $id,
                'current_employer' => true,
                'is_verified' => false,
            ])->first();

            if (is_null($affiliation)) {
                return $this->BadRequestResponse();
            }

            if (!Gate::allows('manage', $affiliation)) {
                return $this->ForbiddenResponse();
            }

            if ($affiliation->is_verified) {
                return $this->ErrorResponse('Affiliation already verified');
            }

            $organisation = Organisation::where('id', $affiliation->organisation_id)->first();
            if (is_null($organisation)) {
                return $this->ErrorResponse('Organisation with id ' .  $affiliation->organisation_id . ' not found');
            }

            $affiliation->is_verified = 0;
            $affiliation->verification_code = Str::uuid()->toString();
            $affiliation->verification_sent_at = Carbon::now();
            $affiliation->save();

            $affiliation->setState(State::STATE_AFFILIATION_EMAIL_VERIFY);

            $this->sendEmailVerificationAffiliation($affiliation);

            // Logic to resend the verification email
            return $this->OKResponse('Verification email resent');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/affiliations/{id}",
     *      operationId="affiliationsUpdate",
     *      x={"internal"="true"},
     *      summary="Update an Affiliation entry",
     *      description="Update an Affiliation entry",
     *      tags={"Affiliations"},
     *      summary="Affiliations@update",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Affiliation entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Affiliation entry ID",
     *         ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Affiliation definition",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Affiliation"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Affiliation"
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function update(UpdateAffiliation $request, int $id): JsonResponse
    {
        try {
            $input = $request->only(app(Affiliation::class)->getFillable());
            $affiliation = Affiliation::findOrFail($id);

            if (!Gate::allows('manage', $affiliation)) {
                return $this->ForbiddenResponse();
            }
            $isCurrentEmail = (strtolower($input['email'] ?? '') === strtolower($request->user()->email));

            $originalAffiliation = $affiliation->getOriginal();

            $unclaimed = $affiliation->organisation->unclaimed;

            if (!$affiliation->is_verified && $input['current_employer']) {
                $affiliation->verification_code = Str::uuid()->toString();
                $affiliation->verification_sent_at = Carbon::now();
                $affiliation->verification_confirmed_at = null;
            }

            // SC: We don't want to allow email updates through this endpoint right now, as it could lead to verification issues.
            // We only allow email updates in the specific case where the user is changing an affiliation from historic to current,
            // _and_ they haven't already set an email before. This cuts out the complex case while still allowing that one case
            // (where we'd not have fired off a verification email yet).
            if (!($input['current_employer'] && !$originalAffiliation['current_employer'] && empty($originalAffiliation['email']))) {
                unset($input['email']);
            }
            $affiliation->fill($input);
            $affiliation->save();
            $affiliation->refresh();

            if ($unclaimed) {
                $affiliation->setState(State::STATE_AFFILIATION_INVITED);
            }

            $custodianHasProjectUser = CustodianHasProjectUser::query()
                ->whereHas('projectHasUser.affiliation', function ($query) use ($affiliation) {
                    $query->where('id', $affiliation->id);
                })->first();

            if (!is_null($custodianHasProjectUser) && $affiliation->current_employer && $affiliation->is_verified && $custodianHasProjectUser->modelState->state->slug === State::STATE_INVITED) {
                $custodianHasProjectUser->setState(State::STATE_PENDING);
            }

            $requiresVerification =
                !$isCurrentEmail
                && $affiliation->current_employer
                && !$affiliation->is_verified;

            if ($requiresVerification) {
                $affiliation->setState(State::STATE_AFFILIATION_EMAIL_VERIFY);

                if (!is_null($custodianHasProjectUser)) {
                    $custodianHasProjectUser->setState(State::STATE_AFFILIATION_EMAIL_VERIFY);
                }

                $this->sendEmailVerificationAffiliation($affiliation);
            } else {
                $affiliation->setState(State::STATE_AFFILIATION_PENDING);
            }

            activity('affiliation')
                ->causedBy(Auth::user())
                ->performedOn($affiliation)
                ->withProperties([
                    'id' => $affiliation->id,
                    'attributes' => $affiliation->getChanges(),
                    'old' => $originalAffiliation,
                ])
                ->event('updated')
                ->log('updated');

            return response()->json([
                'message' => 'success',
                'data' => $affiliation->refresh(),
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/affiliations/verify_email/{verificationCode}",
     *      operationId="affiliationsVerifyEmail",
     *      x={"internal"="true"},
     *      summary="Update an Affiliation entry",
     *      description="Update an Affiliation entry with verification",
     *      tags={"Affiliations"},
     *      summary="Affiliations@verifyEmail",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="verificationCode",
     *         in="path",
     *         description="Email verification code",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="string",
     *            description="Email verification code",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Affiliation"
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function verifyEmail(VerificationEmail $request, string $verificationCode): JsonResponse
    {
        try {
            $loggedInUserId = $request->user()?->id;
            $loggedInUser = User::where('id', $loggedInUserId)->first();

            $affiliation = Affiliation::with('organisation')->where([
                    'verification_code' => $verificationCode,
                    'is_verified'       => 0,
                    'current_employer'  => 1,
                ])
                ->where('verification_sent_at', '>=', now()->subMinutes((int)config('speedi.system.otp_affiliation_validity_minutes')))
                ->first();
            if (is_null($affiliation)) {
                throw new Exception('Affiliation Not Found');
            }

            $organisationId = $affiliation->organisation_id;
            if ($organisationId === -1) {
                throw new Exception('Organisation Not Found in Affiliation');
            }

            $organisation = Organisation::where('id', $organisationId)->first();
            if (is_null($organisation)) {
                throw new Exception('Organisation Not Found');
            }

            if ($organisation->system_approved) {
                $affiliation->setState(State::STATE_AFFILIATION_PENDING);
            } elseif (!is_null($organisation->sro_profile_uri)) {
                $affiliation->setState(State::STATE_AFFILIATION_REVIEW);
            } elseif (!$organisation->unclaimed) {
                $affiliation->setState(State::STATE_AFFILIATION_ACCOUNT_IN_PROGRESS);
            } else {
                $affiliation->setState(State::STATE_AFFILIATION_ORGANISATION_INVITED);
            }
            $custodianHasProjectUser = CustodianHasProjectUser::query()
                ->whereHas('projectHasUser.affiliation', function ($query) use ($affiliation) {
                    $query->where('id', $affiliation->id);
                })->first();

            if (!is_null($custodianHasProjectUser)) {
                $custodianHasProjectUser->setState(State::STATE_PENDING);
            }
            $affiliation->verification_code = null;
            $affiliation->is_verified = 1;
            $affiliation->verification_confirmed_at = Carbon::now();
            $affiliation->save();

            return $this->OKResponse($affiliation);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/training/{id}",
     *      operationId="affiliationDestroy",
     *      x={"internal"="true"},
     *      summary="Delete a affiliation entry from the system by ID",
     *      description="Delete a affiliation entry from the system",
     *      tags={"Affiliation"},
     *      summary="Affiliation@destroy",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Affiliation entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Affiliation entry ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *           ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function destroy(DeleteAffiliation $request, int $id): JsonResponse
    {
        try {
            $loggedInUserId = $request->user()?->id;
            $loggedUser = User::where('id', $loggedInUserId)->first();

            $affiliation = Affiliation::where('id', $id)->first();

            if (!Gate::allows('manage', $affiliation)) {
                return $this->ForbiddenResponse();
            }

            $causer = null;
            if ($loggedUser->user_group === User::GROUP_USERS) {
                $causer = $loggedUser;
            }

            if ($loggedUser->user_group === User::GROUP_ORGANISATIONS) {
                $causer = Organisation::find($loggedUser->organisation_id);
            }

            activity('affiliation')
                ->causedBy($causer)
                ->performedOn($affiliation)
                ->withProperties([
                    'id' => $affiliation->id,
                    'old' => $affiliation,
                ])
                ->event('deleted')
                ->log('deleted');

            Affiliation::where('id', $id)->first()->delete();

            return response()->json([
                'message' => 'success',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateRegistryAffiliation(UpdateAffiliationByRegistry $request, int $registryId, int $id): JsonResponse
    {
        try {
            $loggedInUserId = $request->user()?->id;
            $loggedUser = User::where('id', $loggedInUserId)->first();
            $loggedUserOrgId = $loggedUser?->organisation_id;
            $loggedUserGroup = $loggedUser?->user_group;

            $validated = $request->validate([
                'status' => 'required|string|in:approved,rejected',
            ]);

            $status = strtolower($validated['status']);

            $affiliation = Affiliation::where(
                [
                    'registry_id' => $registryId,
                    'id' => $id
                ]
            )->first();
            if (!$affiliation) {
                return $this->NotFoundResponse();
            }

            if (!Gate::allows('approve', $affiliation)) {
                return $this->ForbiddenResponse();
            }

            if ((!$affiliation->is_verified && $affiliation->current_employer && $status === 'approved') && ((int)$loggedUserOrgId !== (int)$affiliation->organisation_id)) {
                return $this->ErrorResponse('Affiliation is not verified');
            }

            $statusSlugMap = [
                'approved' => State::STATE_AFFILIATION_APPROVED,
                'rejected' => State::STATE_AFFILIATION_REJECTED,
            ];

            if (!array_key_exists($status, $statusSlugMap)) {
                return $this->ErrorResponse('Unknown status');
            }

            $newStateSlug = $statusSlugMap[$status];
            $currentState = $affiliation->getState();

            if (!$affiliation->canTransitionTo($newStateSlug)) {
                return $this->ErrorResponse(
                    'Invalid state transition. ' .
                        $affiliation->getState() .
                        ' => ' . $newStateSlug
                );
            }

            if ($status === 'approved') {
                $affiliation->update([
                    'is_verified' => 1,
                    'verification_confirmed_at' => Carbon::now(),
                ]);

            }

            $affiliation->transitionTo($newStateSlug);

            activity('affiliation')
                ->causedBy(Auth::user())
                ->performedOn(Organisation::find($loggedUserOrgId))
                ->withProperties([
                    'new'   => $newStateSlug,
                    'old'   => $currentState,
                    'user'  => User::where('registry_id', $registryId)->first()
                ])
                ->event('updated')
                ->log('updated');

            // send notification
            $this->sendNotificationOnApprove($status, $registryId, $affiliation);

            return $this->OKResponse($affiliation->getState());
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    public function sendNotificationOnApprove($status, $registryId, $affiliation)
    {
        $organisationId = $affiliation->organisation_id;
        $organisation = Organisation::where('id', $organisationId)->first();
        $user = User::where([
            'registry_id' => $registryId
        ])->first();

        // user
        Notification::send($user, new OrganisationUserAffiliation($user, $organisation, $status, 'user'));

        // organisation
        $userOrganisations = User::where('organisation_id', $organisationId)->get();
        foreach ($userOrganisations as $userOrganisation) {
            Notification::send($userOrganisation, new OrganisationUserAffiliation($user, $organisation, $status, 'organisation'));
        }

        // custodian
        $userCustodianIds = CustodianHasProjectOrganisation::query()
            ->whereHas('projectOrganisation', function ($query) use ($organisationId) {
                $query->where('organisation_id', $organisationId);
            })
            ->select(['custodian_id'])
            ->pluck('custodian_id')->toArray();

        if ($userCustodianIds) {
            $userCustodians = User::whereIn('custodian_user_id', $userCustodianIds)->get();
            foreach ($userCustodians as $userCustodian) {
                Notification::send($userCustodian, new OrganisationUserAffiliation($user, $organisation, $status, 'custodian'));
            }
        }
    }

    public function getWorkflowStates(Request $request)
    {
        return $this->OKResponse(Affiliation::getAllStates());
    }

    public function getWorkflowTransitions(Request $request)
    {
        return $this->OKResponse(Affiliation::getTransitions());
    }
}
