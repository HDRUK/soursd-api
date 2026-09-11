<?php

namespace App\Http\Controllers\Api\V1;

use Hash;
use Exception;
use TriggerEmail;
use App\Models\User;
use App\Models\Project;
use App\Models\Custodian;
use App\Models\Permission;
use App\Models\State;
use Illuminate\Support\Str;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\CustodianUser;
use App\Models\DecisionModel;
use App\Models\PendingInvite;
use App\Http\Traits\Responses;
use App\Models\ProjectHasUser;
use App\Models\ValidationCheck;
use App\Traits\CommonFunctions;
use Illuminate\Http\JsonResponse;
use App\Models\ProjectHasCustodian;
use App\Models\WebhookEventTrigger;
use App\Http\Controllers\Controller;
use App\Models\CustodianModelConfig;
use Illuminate\Support\Facades\Gate;
use App\Models\ProjectHasOrganisation;
use App\Models\CustodianHasProjectUser;
use App\Traits\SearchManagerCollection;
use App\Models\CustodianWebhookReceiver;
use RegistryManagementController as RMC;
use App\Models\CustodianUserHasPermission;
use App\Http\Requests\Custodians\GetCustodian;
use App\Models\CustodianHasProjectOrganisation;
use App\Http\Requests\Custodians\GetUserProject;
use App\Http\Requests\Custodians\DeleteCustodian;
use App\Http\Requests\Custodians\GetStatusAndUser;
use App\Http\Requests\Custodians\GetOrganisationUser;
use App\Http\Requests\Custodians\GetProjectsByCustodian;
use App\Http\Requests\Custodians\GetCustodianByUniqueIdentifier;

/**
 * @OA\Tag(
 *     name="Custodian",
 *     description="API endpoints for managing Custodians"
 * )
 */
class CustodianController extends Controller
{
    use CommonFunctions;
    use Responses;
    use SearchManagerCollection;

    /**
     * @OA\Get(
     *      path="/api/v1/custodians",
     *      operationId="custodianIndex",
     *      x={"internal"="true"},
     *      summary="Return a list of Custodians",
     *      description="Return a list of Custodians",
     *      tags={"Custodian"},
     *      summary="Custodian@index",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Custodian"
     *              )
     *          ),
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
    public function index(Request $request): JsonResponse
    {
        if (!Gate::allows('viewAny', Custodian::class)) {
            return $this->ForbiddenResponse();
        }
        $custodians = Custodian::searchViaRequest()
            ->applySorting()
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return response()->json([
            'message' => 'success',
            'data' => $custodians,
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{id}",
     *      operationId="custodianShow",
     *      x={"internal"="true"},
     *      summary="Return an Custodian entry by ID",
     *      description="Return an Custodian entry by ID",
     *      tags={"Custodian"},
     *      summary="Custodian@show",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Custodian ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Custodian ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Custodian"
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
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
    public function show(GetCustodian $request, int $id): JsonResponse
    {
        if (!Gate::allows('viewAny', Custodian::class)) {
            return $this->ForbiddenResponse();
        }

        $custodian = Custodian::findOrFail($id);
        if ($custodian) {
            return response()->json([
                'message' => 'success',
                'data' => $custodian,
            ], 200);
        }

        return response()->json([
            'message' => 'not found',
            'data' => null,
        ], 404);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/identifier/{id}",
     *      operationId="custodianShowByUniqueIdentifier",
     *      x={"internal"="true"},
     *      summary="Return a Custodian entry by Unique Identifier",
     *      description="Return an Custodian entry by Unique Identifier",
     *      tags={"Custodian"},
     *      summary="Custodian@showByUniqueIdentifier",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Custodian Unique Identifier",
     *         required=true,
     *         example="c3eddb33-db74-4ea7-961a-778740f17e25",
     *         @OA\Schema(
     *            type="string",
     *            description="Custodian Unique Identifier",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Custodian"
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
    public function showByUniqueIdentifier(GetCustodianByUniqueIdentifier $request, string $uniqueIdentifier): JsonResponse
    {
        $custodian = Custodian::where('unique_identifier', $uniqueIdentifier)->first();
        if ($custodian) {
            return response()->json([
                'message' => 'success',
                'data' => $custodian,
            ], 200);
        }

        return response()->json([
            'message' => 'not found',
            'data' => null,
        ], 404);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/custodians",
     *      operationId="custodianStore",
     *      x={"internal"="true"},
     *      summary="Create a Custodian entry",
     *      description="Create a Custodian entry",
     *      tags={"Custodian"},
     *      summary="Custodian@store",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Custodian definition",
     *          @OA\JsonContent(
     *                  @OA\Property(property="name", type="string", example="A Custodian"),
     *                  @OA\Property(property="enabled", type="boolean", example="true")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Custodian"
     *              )
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
    public function store(Request $request): JsonResponse
    {
        if (!Gate::allows('admin')) {
            return $this->ForbiddenResponse();
        }

        try {
            $input = $request->only(app(Custodian::class)->getFillable());

            $signature = Str::random(40);
            $uuid = Str::uuid()->toString();
            $calculatedHash = Hash::make(
                $uuid .
                    ':' . config('speedi.system.custodian_salt_1') .
                    ':' . config('speedi.system.custodian_salt_2')
            );

            $user = User::where([
                'email' => $input['contact_email'],
                'unclaimed' => 0,
            ])->first();

            if (!is_null($user)) {
                $this->ErrorResponse('A user has already registered and claimed this email address.');
            }

            $custodian = Custodian::firstOrCreate(
                ['contact_email' => $input['contact_email']],
                [
                    'name' => $input['name'],
                    'unique_identifier' => $signature,
                    'calculated_hash' => $calculatedHash,
                    'enabled' => $input['enabled'],
                    'idvt_required' => (isset($input['idvt_required']) ? $input['idvt_required'] : false),
                    'client_id' => $uuid,
                ]
            );

            return response()->json([
                'message' => 'success',
                'data' => $custodian->id,
            ], 201);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/custodians/{id}",
     *      operationId="custodianUpdate",
     *      x={"internal"="true"},
     *      summary="Edit a Custodian entry",
     *      description="Edit a Custodian entry",
     *      tags={"Custodian"},
     *      summary="Custodian@update",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Custodian ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Custodian ID",
     *         ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Custodian definition",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="A Custodian"),
     *              @OA\Property(property="enabled", type="boolean", example="true")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Custodian"
     *              )
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
    public function update(GetCustodian $request, int $id): JsonResponse
    {
        try {
            $input = $request->only(app(Custodian::class)->getFillable());
            $custodian = Custodian::findOrFail($id);

            if (!Gate::allows('update', $custodian)) {
                return $this->ForbiddenResponse();
            }

            $custodian->update($input);

            if ($custodian) {
                return response()->json([
                    'message' => 'success',
                    'data' => $custodian,
                ], 200);
            }

            return response()->json([
                'message' => 'failed',
                'data' => null,
                'error' => 'unable to save custodian',
            ], 400);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/custodians/{id}",
     *      operationId="custodianDestroy",
     *      x={"internal"="true"},
     *      summary="Delete a Custodian entry from the system by ID",
     *      description="Delete a Custodian entry from the system",
     *      tags={"Custodian"},
     *      summary="Custodian@destroy",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Custodian entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Custodian entry ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *           ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *           ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success")
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
    public function destroy(DeleteCustodian $request, int $id): JsonResponse
    {
        try {
            $custodian = Custodian::findOrFail((int)$id);
            if (!Gate::allows('delete', $custodian)) {
                return $this->ForbiddenResponse();
            }

            $custodian->delete();

            return response()->json([
                'message' => 'success',
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Stub function for next ticket item
     */
    public function push(Request $request): JsonResponse
    {
        try {
            $projectsAddedCount = 0;
            $organisationsAddedCount = 0;
            $researchersAddedCount = 0;

            // Traverse incoming payload and create entities pushed to us
            $custodianId = $request->header('x-custodian-key');
            $input = $request->all();

            if (! $custodianId) {
                return response()->json([
                    'message' => 'you must be a trusted custodian and provide your custodian-key within the request headers',
                ], 401);
            }

            foreach ($input['projects'] as $p) {
                $project = Project::firstOrCreate(
                    ['unique_id' => $p['unique_id']],
                    [
                        'title' => $p['title'],
                        'lay_summary' => $p['lay_summary'],
                        'public_benefit' => $p['public_benefit'],
                        'request_category_type' => $p['request_category_type'],
                        'technical_summary' => $p['technical_summary'],
                        'other_approval_committees' => $p['other_approval_committees'],
                        'start_date' => $p['start_date'],
                        'end_date' => $p['end_date'],
                        'affiliate_id' => $p['affiliate_id'],
                    ]
                );

                if ($project) {
                    $projectsAddedCount++;
                }
            }

            foreach ($input['organisations'] as $org) {
                $organisation = Organisation::firstOrCreate(
                    ['organisation_unique_id' => $org['organisation_unique_id']],
                    [
                        'organisation_name' => $org['organisation_name'],
                        'address_1' => $org['address_1'],
                        'address_2' => $org['address_2'],
                        'town' => $org['town'],
                        'county' => $org['county'],
                        'country' => $org['country'],
                        'postcode' => $org['postcode'],
                        'lead_applicant_organisation_name' => $org['lead_applicant_organisation_name'],
                        'organisation_unique_id' => $org['organisation_unique_id'],
                        'applicant_names' => $org['applicant_names'],
                        'funders_and_sponsors' => $org['funders_and_sponsors'],
                        'sub_license_arrangements' => $org['sub_license_arrangements'],
                        'companies_house_no' => $org['companies_house_no'],
                        'sector_id' => $org['sector_id'],
                    ]
                );

                if ($organisation) {
                    $organisationsAddedCount++;
                }
            }

            foreach ($input['researchers'] as $researcher) {
                // TBC
                if ($researcher) {
                    $researchersAddedCount++;
                }
            }

            return response()->json([
                'message' => 'success',
                'data' => [
                    'projects_created' => $projectsAddedCount,
                    'organisations_created' => $organisationsAddedCount,
                    'researchers_created' => $researchersAddedCount,

                ],
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{id}/projects",
     *      operationId="custodianGetProjects",
     *      x={"internal"="true"},
     *      summary="Return all projects associated with a custodian",
     *      description="Fetch a list of projects along with pagination details for a specified custodian.",
     *      tags={"Custodian"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the custodian whose projects are to be retrieved",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="per_page", type="integer", example=25),
     *                  @OA\Property(property="total", type="integer", example=24),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          ref="#/components/schemas/Custodian"
     *                      )
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string", example="http://localhost:8100/api/v1/custodians/1/projects?page=1"),
     *                  @OA\Property(property="last_page_url", type="string", example="http://localhost:8100/api/v1/custodians/1/projects?page=1"),
     *                  @OA\Property(property="next_page_url", type="string", example=null),
     *                  @OA\Property(property="prev_page_url", type="string", example=null)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Custodian not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          )
     *      )
     * )
     */
    public function getProjects(GetProjectsByCustodian $request, int $custodianId): JsonResponse
    {
        $custodian = Custodian::findOrFail($custodianId);
        if (! Gate::allows('viewDetailed', $custodian)) {
            return $this->ForbiddenResponse();
        }

        $projects = Project::searchViaRequest()
            ->applySorting()
            ->with(['organisations', 'modelState.state'])
            ->filterByCommon()
            ->filterByState()
            ->whereHas('custodians', function ($query) use ($custodianId) {
                $query->where('custodians.id', $custodianId);
            })
            ->withCount('projectUsers')
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        if ($projects) {
            return response()->json([
                'message' => 'success',
                'data' => $projects,
            ], 200);
        }

        return response()->json([
            'message' => 'not found',
            'data' => null,
        ], 404);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/custodians/{custodianId}/projects",
     *      operationId="custodianAddProject",
     *      x={"internal"="true"},
     *      summary="Create a project for a custodian",
     *      description="Create a project for a custodian",
     *      tags={"Custodian"},
     *      summary="Custodian@addProject",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="custodianId",
     *          in="path",
     *          required=true,
     *          description="ID of the custodian",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Project definition",
     *          @OA\JsonContent(
     *                  @OA\Property(property="title", type="string", example="New project"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="number", example="1")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
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
    public function addProject(GetCustodian $request, int $custodianId): JsonResponse
    {
        try {
            $input = $request->only(app(Project::class)->getFillable());

            $project = Project::create($input);

            ProjectHasCustodian::create([
                'custodian_id' => $custodianId,
                'project_id' => $project->id
            ]);

            if ($project) {
                $project->setState(State::STATE_PROJECT_PENDING);
            }

            return $this->CreatedResponse($project->id);
        } catch (Exception $e) {
            return $this->ErrorResponse($e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{id}/users/{userId}/projects",
     *      operationId="custodianGetUserProjects",
     *      x={"internal"="true"},
     *      summary="Return all custodian projects associated with a user",
     *      description="Fetch a list of custodians projects associated with a user, along with pagination details.",
     *      tags={"Custodian"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The ID of the custodian whose projects are to be retrieved",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="userId",
     *          in="path",
     *          description="The ID of the user whose projects are to be retrieved",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="per_page", type="integer", example=25),
     *                  @OA\Property(property="total", type="integer", example=24),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          ref="#/components/schemas/Project",
     *                          @OA\Property(property="organisations", type="array",
     *                              @OA\Items(
     *                                  ref="#/components/schemas/Organisation",
     *                              )
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string", example="http://localhost:8100/api/v1/custodians/1/users/1/projects?page=1"),
     *                  @OA\Property(property="last_page_url", type="string", example="http://localhost:8100/api/v1/custodians/1/users/1/projects?page=1"),
     *                  @OA\Property(property="next_page_url", type="string", example=null),
     *                  @OA\Property(property="prev_page_url", type="string", example=null)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="user not found")
     *          )
     *      )
     * )
     */
    public function getUserProjects(GetUserProject $request, int $custodianId, int $userId): JsonResponse
    {
        $user = User::with('registry')->where('id', $userId)->first();
        if (is_null($user)) {
            return $this->NotFoundResponse();
        }

        $projects = Project::searchViaRequest()
            ->applySorting()
            ->whereHas('custodianHasProjectUser', function ($chpuq) use ($custodianId, $user) {
                $chpuq->where('custodian_id', $custodianId)
                    ->whereHas('projectHasUser', function ($puq) use ($user) {
                        $puq->where('user_digital_ident', $user->registry->digi_ident);
                    });
            })
            ->with([
                'organisations',
                'modelState.state',
                'custodianHasProjectUser' => fn ($chpuq) => $chpuq
                                ->where('custodian_id', $custodianId)
                                ->whereHas('projectHasUser', function ($puq) use ($user) {
                                    $puq->where('user_digital_ident', $user->registry->digi_ident);
                                })
                                ->with(['modelState.state']),
            ])
            ->filterByCommon()
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($projects);
    }

    /**
     * Hidden from Swagger documentation - no route is registered for this
     * method (routes/api.php wires GET custodians/{id}/organisations to
     * getProjectsOrganisations() instead, a different implementation).
     */
    public function getOrganisations(Request $request, int $custodianId): JsonResponse
    {
        $custodian = Custodian::findOrFail($custodianId);
        if (! Gate::allows('viewDetailed', $custodian)) {
            return $this->ForbiddenResponse();
        }
        $results = Organisation::searchViaRequest()
            ->applySorting()
            ->with(['sroOfficer', 'projects' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Organisation> $query */
                $query->filterByState()
                    ->with("modelState.state");
            }])
            ->whereHas('projects.custodians', function ($query) use ($custodianId) {
                $query->where('custodians.id', $custodianId);
            })
            ->whereHas('projects', function ($query) {
                // LS - Model relation too deep on whereHas, so we have to ignore it here
                /** @phpstan-ignore-next-line */
                $query->filterByState();
            })->getOrganisationsProjects();

        return $this->OKResponse($this->paginateCollection($results));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{custodianId}/projects_users",
     *      operationId="custodianGetProjectsUsers",
     *      x={"internal"="true"},
     *      summary="Get all users associated with custodian's projects",
     *      description="Returns paginated users for all projects under a specific custodian.",
     *      tags={"Custodian"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="custodianId",
     *          in="path",
     *          required=true,
     *          description="Custodian ID",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="List of users",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="per_page", type="integer", example=25),
     *                  @OA\Property(property="total", type="integer", example=3),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="project_id", type="integer", example=1),
     *                          @OA\Property(property="user_digital_ident", type="string", example="$2y$12..."),
     *                          @OA\Property(property="project_role_id", type="integer", example=7),
     *                          @OA\Property(property="primary_contact", type="boolean", example=false),
     *                          @OA\Property(property="affiliation_id", type="integer", example=1),
     *                          @OA\Property(property="role", type="object",
     *                              @OA\Property(property="id", type="integer", example=8),
     *                              @OA\Property(property="name", type="string", example="Student")
     *                          ),
     *                          @OA\Property(property="affiliation", type="object",
     *                              @OA\Property(property="id", type="integer", example=1),
     *                              @OA\Property(property="organisation_id", type="integer", example=1),
     *                              @OA\Property(property="organisation", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="organisation_name", type="string", example="Health Pathways (UK) Limited")
     *                              )
     *                          ),
     *                          @OA\Property(property="registry", type="object",
     *                              @OA\Property(property="id", type="integer", example=1),
     *                              @OA\Property(property="digi_ident", type="string", example="$2y$12..."),
     *                              @OA\Property(property="verified", type="boolean", example=false),
     *                              @OA\Property(property="user", type="object",
     *                                  @OA\Property(property="id", type="integer", example=10),
     *                                  @OA\Property(property="first_name", type="string", example="Dan"),
     *                                  @OA\Property(property="last_name", type="string", example="Ackroyd"),
     *                                  @OA\Property(property="email", type="string", example="dan@example.com"),
     *                                  @OA\Property(property="status", type="string", example="registered")
     *                              )
     *                          ),
     *                          @OA\Property(property="project", type="object",
     *                              @OA\Property(property="id", type="integer", example=2),
     *                              @OA\Property(property="title", type="string", example="Assessing Air Quality Impact...")
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string", example="http://localhost/api/v1/custodians/1/projects_users?page=1"),
     *                  @OA\Property(property="last_page_url", type="string", example="http://localhost/api/v1/custodians/1/projects_users?page=1"),
     *                  @OA\Property(property="next_page_url", type="string", example=null),
     *                  @OA\Property(property="prev_page_url", type="string", example=null)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Custodian not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          )
     *      )
     * )
     */

    public function getProjectsUsers(GetCustodian $request, int $custodianId): JsonResponse
    {
        $custodian = Custodian::findOrFail($custodianId);
        if (! Gate::allows('viewDetailed', $custodian)) {
            return $this->ForbiddenResponse();
        }

        $searchName = $request->input('name');

        $projectUsers = ProjectHasUser::with([
            'registry.user',
            'role',
            'affiliation:id,organisation_id',
            'affiliation.organisation:id,organisation_name',
            'project:id,title',
        ])
            // to be implemented in some way - more discussions and tickets incoming
            //->filterByState();
            ->whereHas('project.custodians', function ($query) use ($custodianId) {
                $query->where('custodian_id', $custodianId);
            })
            ->when(!empty($searchName), function ($query) use ($searchName) {
                $query->where(function ($subQuery) use ($searchName) {
                    $subQuery->whereHas('project', function ($q) use ($searchName) {
                        /** @phpstan-ignore-next-line */
                        $q->searchViaRequest(['title' => $searchName]);
                    });

                    $subQuery->orWhereHas('registry.user', function ($q) use ($searchName) {
                        /** @phpstan-ignore-next-line */
                        $q->searchViaRequest(['name' => $searchName]);
                    });

                    $subQuery->orWhereHas('affiliation.organisation', function ($q) use ($searchName) {
                        /** @phpstan-ignore-next-line */
                        $q->searchViaRequest(['organisation_name' => $searchName]);
                    });
                });
            })
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($projectUsers);
    }

    public function getProjectsOrganisations(GetCustodian $request, int $custodianId): JsonResponse
    {
        $custodian = Custodian::findOrFail($custodianId);
        if (! Gate::allows('viewDetailed', $custodian)) {
            return $this->ForbiddenResponse();
        }

        $searchName = $request->input('name');

        $projectUsers = ProjectHasOrganisation::with([
            'organisation.sroOfficer',
            'project',
        ])
            ->whereHas('project.custodians', function ($query) use ($custodianId) {
                $query->where('custodian_id', $custodianId);
            })
            ->when(!empty($searchName), function ($query) use ($searchName) {
                $query->where(function ($subQuery) use ($searchName) {
                    $subQuery->whereHas('project', function ($q) use ($searchName) {
                        /** @phpstan-ignore-next-line */
                        $q->searchViaRequest(['title' => $searchName]);
                    });

                    $subQuery->orWhereHas('organisation', function ($q) use ($searchName) {
                        /** @phpstan-ignore-next-line */
                        $q->searchViaRequest(['organisation_name' => $searchName]);
                    });
                });
            })
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($projectUsers);
    }



    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{id}/rules",
     *      operationId="custodiansGetRules",
     *      x={"internal"="true"},
     *      summary="Get rules for a specific custodian",
     *      description="Fetches the list of rules associated with the given custodian ID.",
     *      tags={"Custodians"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the custodian",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved rules",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=2),
     *                      @OA\Property(property="name", type="string", example="userLocation"),
     *                      @OA\Property(property="title", type="string", example="User location"),
     *                      @OA\Property(property="description", type="string", example="A User should be located in a country which adheres to equivalent data protection law.")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Custodian not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Custodian not found")
     *          )
     *      )
     * )
     */
    public function getRules(GetCustodian $request, int $custodianId): JsonResponse
    {

        $custodian = Custodian::with('rules')->find($custodianId);
        if (!$custodian) {
            return response()->json(['message' => 'Custodian not found'], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => $custodian->rules
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{custodianId}/custodian_users",
     *      operationId="custodiansGetCustodianUsers",
     *      x={"internal"="true"},
     *      summary="Get list of people for a custodian",
     *      description="Fetches the list of custodian users based on the custodian id.",
     *      tags={"Custodians"},
     *      @OA\Parameter(
     *          name="custodianId",
     *          in="path",
     *          required=true,
     *          description="ID of the custodian",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved custodian users",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/CustodianUser"
     *                  )
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Custodian users not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Custodian users not found")
     *          )
     *      )
     * )
     */
    public function getCustodianUsers(GetCustodian $request, int $custodianId): JsonResponse
    {
        $users = CustodianUser::searchViaRequest()
            ->where('custodian_id', $custodianId)
            ->applySorting()
            ->with("userPermissions.permission")
            ->addSelect([
                'invite_status' => PendingInvite::select('pending_invites.status')
                    ->join('users', 'users.id', '=', 'pending_invites.user_id')
                    ->whereColumn('users.email', 'custodian_users.email')
                    ->limit(1)
            ])
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        if ($users) {
            return $this->OKResponse($users);
        }

        return $this->NotFoundResponse();
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{custodianId}/organisations/{organisationId}/users",
     *      operationId="custodiansGetOrganisationUsers",
     *      x={"internal"="true"},
     *      summary="Get list of people for organisation",
     *      description="Fetches the list of users associated with the given custodian and organisations IDs.",
     *      tags={"Custodians"},
     *      @OA\Parameter(
     *          name="custodianId",
     *          in="path",
     *          required=true,
     *          description="ID of the custodian",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="organisationId",
     *          in="path",
     *          required=true,
     *          description="ID of the organisation",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved organisation users",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/User"
     *                  )
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Organisation users not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Organisation users not found")
     *          )
     *      )
     * )
     */
    public function getOrganisationUsers(GetOrganisationUser $request, int $custodianId, int $organisationId): JsonResponse
    {
        $users = User::searchViaRequest()
                    ->applySorting()
                    ->with([
                        'registry.affiliations' => function ($query) use ($organisationId) {
                            $query->where('organisation_id', $organisationId)
                                ->with('modelState.state');
                        },
                        'registry.affiliations.modelState.state',
                    ])->whereNotNull('registry_id')->whereHas('registry.affiliations', function ($query) use ($organisationId) {
                        $query->where('organisation_id', $organisationId)
                            ->whereHas('modelState.state');
                    })->paginate((int)$this->getSystemConfig('PER_PAGE'));

        if ($users) {
            return $this->OKResponse($users);
        }

        return $this->NotFoundResponse();
    }

    //Hide from swagger docs
    public function invite(GetCustodian $request, int $id): JsonResponse
    {
        try {
            $custodian = Custodian::where('id', $id)->first();
            if (! Gate::allows('update', $custodian)) {
                return $this->ForbiddenResponse();
            }

            $unclaimedUser = RMC::createUnclaimedUser([
                'firstname' => '',
                'lastname' => '',
                'email' => $custodian['contact_email'],
                'user_group' => 'CUSTODIANS',
                'custodian_id' => $id,
                'invited_by' => $request->user()->id,
            ]);

            // CustodianModelConfig - test
            $decisionModels = DecisionModel::all();
            foreach ($decisionModels as $decisionModel) {
                CustodianModelConfig::firstOrCreate(
                    [
                        'custodian_id' => $id,
                        'decision_model_id' => $decisionModel->id,
                    ],
                    [
                        'active' => 1,
                    ]
                );
            }

            // CustodianUser - test
            $custodianUser = CustodianUser::firstOrCreate(
                [
                    'email' => $custodian['contact_email'],
                ],
                [
                    'first_name' => 'Custodian', // I should update it upon activation.
                    'last_name' => 'User', // I should update it upon activation.
                    'email' => $custodian['contact_email'],
                    'password' => Hash::make('t3mpP4ssword!'),
                    'provider' => '',
                    'keycloak_id' => '',
                    'custodian_id' => $id,
                ]
            );

            // update User - test
            User::where('id', $unclaimedUser->id)->update([
                'custodian_user_id' => $custodianUser->id,
            ]);

            // Permission - test
            $permission = Permission::where('name', '=', 'CUSTODIAN_ADMIN')->first();
            CustodianUserHasPermission::firstOrCreate(
                [
                    'custodian_user_id' => $custodianUser->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'custodian_user_id' => $custodianUser->id,
                    'permission_id' => $permission->id,
                ]
            );

            // webhooks - test
            $webhookEventTriggers = WebhookEventTrigger::where('enabled', true)->get();
            foreach ($webhookEventTriggers as $webhookEventTrigger) {
                CustodianWebhookReceiver::firstOrCreate(
                    [
                        'custodian_id' => $id,
                        'webhook_event' => $webhookEventTrigger->id,
                    ],
                    [
                        'custodian_id' => $id,
                        'url' => 'https://webhook.site/4c812c72-3db1-4162-9160-5a798b52306c', // free webhook receiver
                        'webhook_event' => $webhookEventTrigger->id,
                    ]
                );
            }

            // custodian has validations check
            $validationChecks = ValidationCheck::query()
                ->whereNull('custodian_id')
                ->orWhere('custodian_id', $id)
                ->get();

            foreach ($validationChecks as $validationCheck) {
                $newValidationCheck = $validationCheck->replicate();
                $newValidationCheck->custodian_id = $id;
                $newValidationCheck->save();
            }

            $input = [
                'type' => 'CUSTODIAN',
                'to' => $custodian->id,
                'unclaimed_user_id' => $unclaimedUser->id,
                'by' => $id,
                'identifier' => 'custodian_invite'
            ];

            TriggerEmail::spawnEmail($input);

            return response()->json([
                'message' => 'success',
                'data' => $custodian,
            ], 201);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{custodianId}/projectUsers/{projectUserId}/statuses",
     *      operationId="custodiansGetStatusesUsers",
     *      x={"internal"="true"},
     *      summary="Get statuses for a user in a project/organisation/custodian",
     *      description="Fetches the user statuses given custodian and organisations and project and user IDs.",
     *      tags={"Custodians"},
     *      @OA\Parameter(
     *          name="custodianId",
     *          in="path",
     *          required=true,
     *          description="ID of the custodian",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="projectUserId",
     *          in="path",
     *          required=true,
     *          description="ID of the project user",
     *          @OA\Schema(type="integer")
     *      ),

     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved organisation users",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/User"
     *                  )
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Organisation users not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Organisation users not found")
     *          )
     *      )
     * )
     */
    public function getStatusesUsers(GetStatusAndUser $request, int $custodianId, int $projectUserId): JsonResponse
    {
        $projectStatus = $this->getProjectStatus($custodianId, $projectUserId);
        $organisationStatus = $this->getOrganisationStatus($custodianId, $projectUserId);
        $validationState = $this->getValidationState($custodianId, $projectUserId);
        $affiliationStatus = $this->getAffiliationStatus($custodianId, $projectUserId);

        $response = [
                'validation_state' => $validationState,
                'project_status' => $projectStatus,
                'organisation_status' => $organisationStatus,
                'affiliation_status' => $affiliationStatus,
        ];

        return $this->OKResponse($response);
    }

    // Hide from swagger docs
    private function getValidationState(int $custodianId, int $projectUserId)
    {
        $record = CustodianHasProjectUser::with([
                'modelState.state',
            ])
        ->where('custodian_id', $custodianId)
        ->where('project_has_user_id', $projectUserId)
        ->first();

        return $record->modelState ?? null;
    }

    // // Hide from swagger docs
    private function getProjectStatus(int $custodianId, int $projectUserId)
    {
        $records = ProjectHasUser::with([
            'project.modelState.state'
        ])
        ->where('id', $projectUserId)
        ->first();

        return optional($records->project)->modelState ?? null;
    }

    // // Hide from swagger docs
    private function getOrganisationStatus(int $custodianId, int $projectUserId)
    {
        $projectHasUser = ProjectHasUser::where('id', $projectUserId)->first();

        $records = CustodianHasProjectOrganisation::query()
            ->select('custodian_has_project_has_organisation.*')
            ->where('custodian_has_project_has_organisation.custodian_id', $custodianId)
            ->with([
                'modelState.state'
            ])
            ->join('project_has_organisations', 'custodian_has_project_has_organisation.project_has_organisation_id', '=', 'project_has_organisations.id')
            ->join('project_has_users', 'project_has_organisations.project_id', '=', 'project_has_users.project_id')
            ->where('project_has_users.project_id', $projectHasUser->project_id)
            ->first();

        return $records->modelState ?? null;
    }

    // // Hide from swagger docs
    private function getAffiliationStatus(int $custodianId, int $projectUserId)
    {
        $record = CustodianHasProjectUser::with([
                'projectHasUser.affiliation.modelState.state',
            ])
        ->where('custodian_has_project_has_user.custodian_id', $custodianId)
        ->where('custodian_has_project_has_user.project_has_user_id', $projectUserId)
        ->first();

        return optional($record->projectHasUser)->affiliation->modelState ?? null;
    }
}
