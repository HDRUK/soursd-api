<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use Hash;
use Keycloak;
use Exception;
use RegistryManagementController as RMC;
use App\Services\DecisionEvaluatorService as DES;
use App\Models\User;
use App\Models\Registry;
use App\Models\Project;
use App\Models\ProjectHasUser;
use App\Models\UserHasCustodianApproval;
use App\Models\UserHasCustodianPermission;
use App\Models\UserHasDepartments;
use App\Models\Organisation;
use App\Http\Requests\Users\CreateUser;
use App\Http\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\ProjectUserCustodianApproval;
use App\Traits\CommonFunctions;
use App\Traits\CheckPermissions;
use Illuminate\Support\Facades\Gate;
use TriggerEmail;

class UserController extends Controller
{
    use CommonFunctions;
    use CheckPermissions;
    use Responses;

    protected $decisionEvaluator = null;

    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      summary="Return a list of Users",
     *      description="Return a list of Users",
     *      tags={"User"},
     *      summary="User@index",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="created_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="updated_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="first_name", type="string", example="A"),
     *                  @OA\Property(property="last_name", type="string", example="Researcher"),
     *                  @OA\Property(property="email", type="string", example="person@somewhere.com"),
     *                  @OA\Property(property="email_verified_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="consent_scrape", type="boolean", example="true"),
     *                  @OA\Property(property="public_opt_in", type="boolean", example="true"),
     *                  @OA\Property(property="declaration_signed", type="boolean", example="true"),
     *                  @OA\Property(property="organisation_id", type="integer", example="123"),
     *                  @OA\Property(property="orcid_scanning", type="integer", example="1"),
     *                  @OA\Property(property="orcid_scanning_completed_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="location", type="string", example="United Kingdom"),
     *                  @OA\Property(property="t_and_c_agreed", type="boolean", example="true"),
     *                  @OA\Property(property="t_and_c_agreement_date", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="uksa_registered", type="boolean", example="true"),
     *                  @OA\Property(property="is_sro", type="boolean", example="false")
     *
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        if (!Gate::allows('viewAny', User::class)) {
            return $this->ForbiddenResponse();
        }
        $this->decisionEvaluator = new DES($request);

        $users = User::searchViaRequest()
            ->filterByState()
            ->with([
                'permissions',
                'registry',
                'registry.files',
                'registry.affiliations',
                'registry.affiliations.organisation:id,organisation_name',
                'pendingInvites',
                'organisation',
                'departments',
                'registry.education',
                'registry.trainings',
                'registry.identity',
                'modelState'
            ])->paginate((int)$this->getSystemConfig('PER_PAGE'));

        $evaluations = $this->decisionEvaluator->evaluate($users->items(), true);
        $users->setCollection($users->getCollection()->map(function ($user) use ($evaluations) {
            $user->evaluation = $evaluations[$user->id] ?? null;
            return $user;
        }));


        return response()->json(
            [
                'message' => 'success',
                'data' => $users,
            ],
            200
        );
    }

    public function validateUserRequest(Request $request): JsonResponse
    {
        $input = $request->only(['email']);
        $retVal = User::searchByEmail($input['email']);

        $returnPayload = [];

        if ($retVal) {
            $user = User::where('registry_id', $retVal->registry_id)->first();
            $returnPayload = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'public_opt_in' => $user->public_opt_in,
                'digital_identifier' => Registry::where('id', $retVal->registry_id)->select('digi_ident')->first()['digi_ident'],
                'email' => $input['email'],
                'identity_source' => $retVal->source,
            ];

            return response()->json([
                'message' => 'success',
                'data' => $returnPayload,
            ], 200);
        }

        return response()->json([
            'message' => 'not_found',
            'data' => null,
        ], 404);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      summary="Return a User entry by ID",
     *      description="Return a User entry by ID",
     *      tags={"User"},
     *      summary="User@show",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         example="1",
     *
     *         @OA\Schema(
     *            type="integer",
     *            description="User ID",
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="created_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="updated_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="first_name", type="string", example="A"),
     *                  @OA\Property(property="last_name", type="string", example="Researcher"),
     *                  @OA\Property(property="email", type="string", example="person@somewhere.com"),
     *                  @OA\Property(property="email_verified_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="consent_scrape", type="boolean", example="true"),
     *                  @OA\Property(property="public_opt_in", type="boolean", example="true"),
     *                  @OA\Property(property="declaration_signed", type="boolean", example="true"),
     *                  @OA\Property(property="organisation_id", type="integer", example="123"),
     *                  @OA\Property(property="orcid_scanning", type="integer", example="1"),
     *                  @OA\Property(property="orcid_scanning_completed_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="location", type="string", example="United Kingdom"),
     *                  @OA\Property(property="t_and_c_agreed", type="boolean", example="true"),
     *                  @OA\Property(property="t_and_c_agreement_date", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="status", type="string", example="registered"),
     *                  @OA\Property(property="uksa_registered", type="boolean", example="true"),
     *                  @OA\Property(property="is_sro", type="boolean", example="false")
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $this->decisionEvaluator = new DES($request);

            $user = User::with([
                'permissions',
                'registry',
                'registry.files',
                'registry.affiliations',
                'pendingInvites',
                'organisation',
                'departments',
                'registry.identity',
                'registry.education',
                'registry.trainings',
            ])->where('id', $id)->first();

            if (!Gate::allows('view', $user)) {
                return $this->ForbiddenResponse();
            }

            $user['rules'] = $this->decisionEvaluator->evaluate($user);

            return response()->json([
                'message' => 'success',
                'data' => $user
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getHistory(Request $request, int $id): JsonResponse
    {
        try {
            // Post-MVP - this should be an audit log for the user...
            $user = User::findOrFail($id);
            if (!Gate::allows('view', $user)) {
                return $this->ForbiddenResponse();
            }

            $approvalLog = ProjectUserCustodianApproval::with('custodian:id,name')->where(['user_id' => $user->id])->get();

            $approvalHistory = $approvalLog->map(function ($log) {
                $custodian = $log->custodian->name;
                return [
                    'message' => $log->approved ? 'custodian_approved' : 'custodian_rejected',
                    'details' => $log->approved
                        ? $custodian . ' approved user: ' . ($log->comment ?? 'No comment')
                        : $custodian . ' rejected user: ' . ($log->comment ?? 'No comment'),
                    'created_at' => $log->created_at,
                ];
            });

            // placeholder to give some history
            $data = collect([
                [
                    'message' => 'profile_created',
                    'created_at' => $user->created_at,
                ],
            ])
                ->merge(
                    $user->actionLogs
                        ->whereNotNull("completed_at")
                        ->map(function ($log) {
                            return [
                                'message' => $log->action,
                                'created_at' => $log->completed_at,
                            ];
                        })
                )
                ->merge($approvalHistory)
                ->sortByDesc('created_at')
                ->values()
                ->toArray();

            return response()->json([
                'message' => 'success',
                'data' => $data,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      summary="Create a User entry",
     *      description="Create a User entry",
     *      tags={"Users"},
     *      summary="Users@store",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="User definition",
     *
     *          @OA\JsonContent(
     *
     *                  @OA\Property(property="first_name", type="string", example="A"),
     *                  @OA\Property(property="last_name", type="string", example="Researcher"),
     *                  @OA\Property(property="email", type="string", example="someone@somewhere.com"),
     *                  @OA\Property(property="password", type="string", example="str0ng12P4ssword?"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="created_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="updated_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="first_name", type="string", example="A"),
     *                  @OA\Property(property="last_name", type="string", example="Researcher"),
     *                  @OA\Property(property="email", type="string", example="person@somewhere.com"),
     *                  @OA\Property(property="email_verified_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="consent_scrape", type="boolean", example="true"),
     *                  @OA\Property(property="public_opt_in", type="boolean", example="true"),
     *                  @OA\Property(property="declaration_signed", type="boolean", example="true"),
     *                  @OA\Property(property="organisation_id", type="integer", example="123"),
     *                  @OA\Property(property="orcid_scanning", type="integer", example="1"),
     *                  @OA\Property(property="orcid_scanning_completed_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="status", type="string", example="registered"),
     *                  @OA\Property(property="uksa_registered", type="boolean", example="true"),
     *                  @OA\Property(property="is_sro", type="boolean", example="false")
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function store(CreateUser $request): JsonResponse
    {
        if (!Gate::allows('create', User::class)) {
            return $this->ForbiddenResponse();
        }
        try {
            $input = $request->all();

            $user = User::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'provider' => isset($input['provider']) ? $input['provider'] : '',
                'registry_id' => isset($input['registry_id']) ? $input['registry_id'] : null,
                'keycloak_id' => null,
                'user_group' => Keycloak::determineUserGroup($input),
                'consent_scrape' => isset($input['consent_scrape']) ? $input['consent_scrape'] : 0,
                'public_opt_in' => isset($input['public_opt_in']) ? $input['public_opt_in'] : false,
                'declaration_signed' => isset($input['declaration_signed']) ? $input['declaration_signed'] : false,
                'organisation_id' => isset($input['organisation_id']) ? $input['organisation_id'] : null,
                'uksa_registered' => isset($input['uksa_registered']) ? $input['uksa_registered'] : 0,
                'is_sro' => isset($input['is_sro']) ? $input['is_sro'] : 0,
            ]);

            // TODO - Close Pending invite when we're sure how org id is handled

            return response()->json([
                'message' => 'success',
                'data' => $user->id,
            ], 201);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    //Hide from swagger docs
    public function invite(Request $request): JsonResponse
    {
        if (!Gate::allows('invite', User::class)) {
            return $this->ForbiddenResponse();
        }
        try {
            $input = $request->all();

            $unclaimedUser = RMC::createUnclaimedUser([
                'firstname' => $input['first_name'],
                'lastname' => $input['last_name'],
                'email' => $input['email'],
                'user_group' => 'USERS'
            ]);

            $input = [
                'type' => 'USER_WITHOUT_ORGANISATION',
                'to' => $unclaimedUser->id,
                'unclaimed_user_id' => $unclaimedUser->id,
                'identifier' => 'researcher_without_organisation_invite'
            ];

            TriggerEmail::spawnEmail($input);

            return response()->json([
                'message' => 'success',
                'data' => $unclaimedUser,
            ], 201);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/users/{id}",
     *      summary="Edit a User entry",
     *      description="Edit a User entry",
     *      tags={"User"},
     *      summary="User@update",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         example="1",
     *
     *         @OA\Schema(
     *            type="integer",
     *            description="User ID",
     *         ),
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="User definition",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="first_name", type="string", example="A"),
     *              @OA\Property(property="last_name", type="string", example="Researcher"),
     *              @OA\Property(property="email", type="string", example="someone@somewhere.com"),
     *              @OA\Property(property="password", type="string", example="str0ng12P4ssword?"),
     *              @OA\Property(property="orc_id", type="string", example="0000-0000-0000-0000"),
     *              @OA\Property(property="location", type="string", example="United Kingdom"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="created_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="updated_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="first_name", type="string", example="A"),
     *                  @OA\Property(property="last_name", type="string", example="Researcher"),
     *                  @OA\Property(property="email", type="string", example="person@somewhere.com"),
     *                  @OA\Property(property="email_verified_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="consent_scrape", type="boolean", example="true"),
     *                  @OA\Property(property="public_opt_in", type="boolean", example="true"),
     *                  @OA\Property(property="declaration_signed", type="boolean", example="true"),
     *                  @OA\Property(property="organisation_id", type="integer", example="123"),
     *                  @OA\Property(property="orc_id", type="string", example="0000-0000-0000-0000"),
     *                  @OA\Property(property="orcid_scanning", type="integer", example="1"),
     *                  @OA\Property(property="orcid_scanning_completed_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="t_and_c_agreed", type="boolean", example="true"),
     *                  @OA\Property(property="t_and_c_agreement_date", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="status", type="string", example="registered"),
     *                  @OA\Property(property="uksa_registered", type="boolean", example="true"),
     *                  @OA\Property(property="is_sro", type="boolean", example="false")
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $input = $request->all();

            $user = User::where('id', $id)->first();

            if (!Gate::allows('update', $user)) {
                return $this->ForbiddenResponse();
            }

            $user->first_name = isset($input['first_name']) ? $input['first_name'] : $user->first_name;
            $user->last_name = isset($input['last_name']) ? $input['last_name'] : $user->last_name;
            $user->email = isset($input['email']) ? $input['email'] : $user->email;
            $user->password = isset($input['password']) ? Hash::make($input['password']) : $user->password;
            $user->registry_id = isset($input['registry_id']) ? $input['registry_id'] : $user->registry_id;
            $user->consent_scrape = isset($input['consent_scrape']) ? $input['consent_scrape'] : $user->consent_scrape;
            $user->public_opt_in = isset($input['public_opt_in']) ? $input['public_opt_in'] : $user->public_opt_in;
            $user->declaration_signed = isset($input['declaration_signed']) ? $input['declaration_signed'] : $user->declaration_signed;
            $user->organisation_id = isset($input['organisation_id']) ? $input['organisation_id'] : $user->organisation_id;
            $user->orc_id = isset($input['orc_id']) ? $input['orc_id'] : $user->orc_id;
            $user->location = isset($input['location']) ? $input['location'] : $user->location;
            $user->t_and_c_agreed = isset($input['t_and_c_agreed'])
                ? filter_var($input['t_and_c_agreed'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                : $user->t_and_c_agreed;
            $user->t_and_c_agreement_date = isset($input['lt_and_c_agreement_date']) ? $input['t_and_c_agreement_date'] : $user->t_and_c_agreement_date;
            $user->uksa_registered = isset($input['uksa_registered']) ? $input['uksa_registered'] : $user->uksa_registered;
            $user->is_sro = isset($input['is_sro']) ? $input['is_sro'] : $user->is_sro;

            if ($user->save()) {
                return response()->json([
                    'message' => 'success',
                    'data' => $user,
                ], 200);
            }

            return response()->json([
                'message' => 'failed',
                'data' => null,
                'error' => 'unable to save user',
            ], 409);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/users/{id}",
     *      summary="Edit a User entry",
     *      description="Edit a User entry",
     *      tags={"User"},
     *      summary="User@edit",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         example="1",
     *
     *         @OA\Schema(
     *            type="integer",
     *            description="User ID",
     *         ),
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="User definition",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="first_name", type="string", example="A"),
     *              @OA\Property(property="last_name", type="string", example="Researcher"),
     *              @OA\Property(property="email", type="string", example="someone@somewhere.com"),
     *              @OA\Property(property="password", type="string", example="str0ng12P4ssword?"),
     *              @OA\Property(property="orc_id", type="string", example="0000-0000-0000-0000"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="created_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="updated_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="first_name", type="string", example="A"),
     *                  @OA\Property(property="last_name", type="string", example="Researcher"),
     *                  @OA\Property(property="email", type="string", example="person@somewhere.com"),
     *                  @OA\Property(property="email_verified_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="consent_scrape", type="boolean", example="true"),
     *                  @OA\Property(property="public_opt_in", type="boolean", example="true"),
     *                  @OA\Property(property="declaration_signed", type="boolean", example="true"),
     *                  @OA\Property(property="organisation_id", type="integer", example="123"),
     *                  @OA\Property(property="orc_id", type="string", example="0000-0000-0000-0000"),
     *                  @OA\Property(property="orcid_scanning", type="integer", example="1"),
     *                  @OA\Property(property="orcid_scanning_completed_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="location", type="string", example="United Kingdom"),
     *                  @OA\Property(property="t_and_c_agreed", type="boolean", example="true"),
     *                  @OA\Property(property="t_and_c_agreement_date", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="status", type="string", example="registered"),
     *                  @OA\Property(property="uksa_registered", type="boolean", example="true"),
     *                  @OA\Property(property="is_sro", type="boolean", example="false")
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function edit(Request $request, int $id): JsonResponse
    {
        try {
            $input = $request->all();
            $user = User::find($id);

            if (!Gate::allows('update', $user)) {
                return $this->ForbiddenResponse();
            }

            $originalUser = clone $user;

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if (isset($input['department_id']) && $input['department_id'] !== 0 && $input['department_id'] !== null) {
                UserHasDepartments::where('user_id', $user->id)->delete();
                UserHasDepartments::create([
                    'user_id' => $user->id,
                    'department_id' => $request['department_id'],
                ]);
            };

            $input = $request->only(app(User::class)->getFillable());

            if (isset($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            }

            $updated = $user->update($input);

            if ($updated) {

                return response()->json([
                    'message' => 'success',
                    'data' => User::find($id)
                ], 200);
            }

            return response()->json([
                'message' => 'failed',
                'data' => null,
                'error' => 'unable to save user',
            ], 409);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      summary="Delete a User entry from the system by ID",
     *      description="Delete a User entry from the system",
     *      tags={"User"},
     *      summary="User@destroy",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User entry ID",
     *         required=true,
     *         example="1",
     *
     *         @OA\Schema(
     *            type="integer",
     *            description="User entry ID",
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *           ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            if (!Gate::allows('delete', $user)) {
                return $this->ForbiddenResponse();
            }
            $user->delete();

            UserHasCustodianPermission::where('user_id', $id)->delete();
            UserHasCustodianApproval::where('user_id', $id)->delete();

            return response()->json([
                'message' => 'success',
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function searchUsersByNameAndProfessionalEmail(Request $request): JsonResponse
    {
        if (!Gate::allows('viewAny', User::class)) {
            return $this->ForbiddenResponse();
        }
        try {
            $input = $request->only([
                'first_name',
                'last_name',
                'email',
            ]);

            $results = DB::select(
                "
                SELECT
                    u.id AS id,
                    u.first_name AS first_name,
                    u.last_name AS last_name,
                    u.registry_id AS registry_id,
                    a.email AS email,
                    a.id AS affiliation_id,
                    o.id AS organisation_id,
                    o.organisation_name AS organisation_name
                FROM users u
                JOIN registry_has_affiliations rha
                    ON rha.registry_id = u.registry_id
                LEFT JOIN affiliations a
                    ON a.id = rha.affiliation_id
                JOIN organisations o
                    ON o.id = a.organisation_id
                WHERE
                    user_group='USERS'
                AND 
                (
                    u.first_name LIKE ?
                )
                OR
                (
                    u.last_name LIKE ?
                )
                OR
                (
                    a.email LIKE ?
                )
                ",
                [
                    $input['first_name'],
                    $input['last_name'],
                    $input['email']
                ]
            );

            $records = collect($results)->toArray();
            return $this->OKResponse($records);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function userProjects(Request $request, int $id): JsonResponse
    {
        $user = User::with('registry')->findOrFail($id);
        if (!Gate::allows('view', $user)) {
            return $this->ForbiddenResponse();
        }

        $projectIds = ProjectHasUser::where('user_digital_ident', $user->registry->digi_ident)
            ->pluck('project_id')
            ->toArray();

        $projects = Project::whereIn('id', $projectIds)
            ->withCount('projectUsers')
            ->with(['organisations', 'modelState.state'])
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));
        ;
        return $this->OKResponse($projects);
    }

    public function fakeEndpointForTesting(Request $request): JsonResponse
    {
        $checkGroups = $this->hasGroups($request, ['admin']);
        if (!$checkGroups) {
            return response()->json([
                'message' => 'you do not have the required permissions to view this data',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }

        $users = User::all();
        return response()->json([
            'message' => 'success',
            'data' => $users,
        ], Response::HTTP_OK);
    }
}
