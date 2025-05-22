<?php

namespace App\Http\Controllers\Api\V1;

use Keycloak;
use Exception;
use RegistryManagementController as RMC;
use Carbon\Carbon;
use App\Models\Organisation;
use App\Models\OrganisationDelegate;
use App\Models\PendingInvite;
use App\Models\User;
use App\Models\Affiliation;
use App\Models\RegistryHasAffiliation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\DebugLog;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function registerKeycloakUser(Request $request): JsonResponse
    {
        try {
            $tokenParts = explode('Bearer ', $request->headers->get('Authorization'));
            $token = trim($tokenParts[1] ?? '');

            $response = Keycloak::getUserInfo($token);
            $payload = $response->json();

            DebugLog::create([
                'class' => AuthController::class,
                'log' => json_encode(array_keys($payload))
            ]);

            $user = RMC::createNewUser($payload, $request);

            if ($user) {
                if (isset($user['unclaimed_user_id'])) {
                    $unclaimedUser = User::where('id', $user['unclaimed_user_id'])->first();
                    $pendingInvite = PendingInvite::where('user_id', $user['unclaimed_user_id'])->first();
                    if ($pendingInvite) {

                        $registryId = $unclaimedUser->registry_id;
                        $organisationId = $pendingInvite->organisation_id;

                        $aff = Affiliation::create([
                            'organisation_id' => $organisationId,
                            'member_id' => '',
                            'relationship' => null,
                            'from' => null,
                            'to' => null,
                            'department' => null,
                            'role' => null,
                            'email' => $unclaimedUser->email,
                            'ror' => null,
                            'registry_id' => $registryId,
                        ]);

                        RegistryHasAffiliation::create([
                            'affiliation_id' => $aff->id,
                            'registry_id' => $registryId,
                        ]);

                        $pendingInvite->invite_accepted_at = Carbon::now();
                        $pendingInvite->status = config('speedi.invite_status.COMPLETE');
                        $pendingInvite->save();
                    }


                    return response()->json([
                        'message' => 'success',
                        'data' => $unclaimedUser,
                    ], 201);
                }

                return response()->json([
                    'message' => 'success',
                    'data' => null,
                ], 201);
            }

            return response()->json([
                'message' => 'failed',
                'data' => null,
            ], 400);
        } catch (Exception $e) {
            DebugLog::create([
                'class' => AuthController::class,
                'log' => $e->getMessage()
            ]);
            throw new Exception($e);
        }
    }

    public function me(Request $request): JsonResponse
    {
        $token = Auth::token();
        if (!$token) {
            return response()->json([
                'message' => 'unauthorised',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }

        $arr = json_decode($token, true);

        if (!isset($arr['sub'])) {
            return response()->json([
                'message' => 'not found',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $user = User::where('keycloak_id', $arr['sub'])->first();

        //If unclaimed user and account type and just logged in

        if (!$user) {
            return response()->json([
                'message' => 'not found',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'success',
            'data' => $user,
        ], Response::HTTP_OK);
    }

    public function registerUser(Request $request): JsonResponse
    {
        $input = $request->all();
        $retVal = Keycloak::create([
            'email' => $input['email'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'password' => $input['password'],
            'is_researcher' => true,
        ]);

        if ($retVal['success']) {
            $user = User::where('email', $input['email'])->first();

            return response()->json([
                'message' => 'success',
                'data' => $user,
            ], 201);
        }

        return response()->json([
            'message' => 'failed',
            'data' => $retVal['error'],
        ], 409); // Send a "CONFLICT" as record likely exists.);
    }

    public function registerCustodian(Request $request): JsonResponse
    {
        $input = $request->all();

        $retVal = Keycloak::create([
            'email' => $input['email'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'password' => $input['password'],
            'is_custodian' => true,
        ]);
        if ($retVal['success']) {
            $user = User::where('email', $input['email'])->first();

            return response()->json([
                'message' => 'success',
                'data' => $user,
            ], 201);
        }

        return response()->json([
            'message' => 'failed',
            'data' => $retVal['error'],
        ], 409); // Send a "CONFLICT" as record likely exists.
    }

    public function registerOrganisation(Request $request): JsonResponse
    {
        $input = $request->all();

        $retVal = Keycloak::create([
            'email' => $input['email'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'password' => $input['password'],
            'is_organisation' => true,
        ]);

        if ($retVal['success']) {
            $user = User::where('email', $input['email'])->first();
            $organisation = Organisation::create([
                'organisation_name' => $input['organisation_name'],
                'lead_applicant_organisation_email' => $input['lead_applicant_organisation_email'],
                'lead_applicant_organisation_name' => $input['lead_applicant_organisation_name'],
                'companies_house_no' => $input['companies_house_no'],
                'ce_certified' => $input['ce_certified'],
                'ce_certification_num' => $input['ce_certification_num'],
                'iso_27001_certified' => $input['iso_27001_certified'],
                'dsptk_ods_code' => $input['dsptk_ods_code'],
                'address_1' => $input['address_1'],
                'address_2' => $input['address_2'],
                'town' => $input['town'],
                'county' => $input['county'],
                'country' => $input['country'],
                'postcode' => $input['postcode'],
                'organisation_unique_id' => Str::random(40),
                'applicant_names' => '',
            ]);

            $user->organisation_id = $organisation->id;
            $user->save();

            if (isset($input['dpo_name']) && isset($input['dpo_email'])) {
                $parts = explode(' ', $input['dpo_name']);
                OrganisationDelegate::create([
                    'first_name' => $parts[0],
                    'last_name' => $parts[1],
                    'email' => $input['dpo_email'],
                    'is_dpo' => 1,
                    'is_hr' => 0,
                    'priority_order' => 0,
                    'organisation_id' => $organisation->id,
                ]);
            }

            if (isset($input['hr_name'])) {
                $parts = explode(' ', $input['hr_name']);
                OrganisationDelegate::create([
                    'first_name' => $parts[0],
                    'last_name' => $parts[1],
                    'email' => $input['hr_email'],
                    'is_dpo' => 0,
                    'is_hr' => 1,
                    'priority_order' => 0,
                    'organisation_id' => $organisation->id,
                ]);
            }

            return response()->json([
                'message' => 'success',
                'data' => $user,
            ], 201);
        }

        return response()->json([
            'message' => 'failed',
            'data' => $retVal['error'],
        ], 409); // Send a "CONFLICT" status, as a record likely exists
    }
}
