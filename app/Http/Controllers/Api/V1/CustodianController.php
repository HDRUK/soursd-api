<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use Exception;
use Hash;
use RegistryManagementController as RMC;
use TriggerEmail;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Custodian;
use App\Models\Organisation;
use App\Models\CustodianHasRule;
use App\Models\Rules;
use App\Models\Project;
use App\Traits\CommonFunctions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Traits\Responses;
use App\Traits\SearchManagerCollection;

class CustodianController extends Controller
{
    use CommonFunctions;
    use Responses;
    use SearchManagerCollection;

    /**
     * @OA\Get(
     *      path="/api/v1/custodians",
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
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
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
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function showByUniqueIdentifier(Request $request, string $id): JsonResponse
    {
        $custodian = Custodian::where('unique_identifier', $id)->first();
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
        try {
            $input = $request->all();

            $signature = Str::random(40);
            $uuid = Str::uuid()->toString();
            $calculatedHash = Hash::make(
                $uuid .
                 ':'.env('CUSTODIAN_SALT_1').
                 ':'.env('CUSTODIAN_SALT_2')
            );

            $custodian = Custodian::create([
                'name' => $input['name'],
                'unique_identifier' => $signature,
                'calculated_hash' => $calculatedHash,
                'contact_email' => $input['contact_email'],
                'enabled' => $input['enabled'],
                'idvt_required' => (isset($input['idvt_required']) ? $input['idvt_required'] : false),
                'client_id' => $uuid,
            ]);

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
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $input = $request->only(app(Custodian::class)->getFillable());
            $custodian = Custodian::findOrFail($id);
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
     * @OA\Patch(
     *      path="/api/v1/custodians/{id}",
     *      summary="Edit a Custodian entry",
     *      description="Edit a Custodian entry",
     *      tags={"Custodian"},
     *      summary="Custodian@edit",
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
    public function edit(Request $request, int $id): JsonResponse
    {
        try {
            $input = $request->all();

            $custodian = Custodian::where('id', $id)->first();

            $custodian->invite_accepted_at = isset($input['invite_accepted_at']) ? $input['invite_accepted_at'] : $custodian->invite_accepted_at;
            $custodian->name = isset($input['name']) ? $input['name'] : $custodian->name;
            $custodian->contact_email = isset($input['contact_email']) ? $input['contact_email'] : $custodian->contact_email;
            $custodian->enabled = isset($input['enabled']) ? $input['enabled'] : $custodian->enabled;
            $custodian->idvt_required = isset($input['idvt_required']) ? $input['idvt_required'] : $custodian->idvt_required;
            $custodian->gateway_app_id = isset($input['gateway_app_id']) ? $input['gateway_app_id'] : $custodian->gateway_app_id;
            $custodian->gateway_client_id = isset($input['gateway_client_id']) ? $input['gateway_client_id'] : $custodian->gateway_client_id;

            if ($custodian->save()) {
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
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            Custodian::where('id', $id)->delete();

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
     *      path="/api/v1/custodian/{custodianId}/projects",
     *      summary="Return all projects associated with a custodian",
     *      description="Fetch a list of projects along with pagination details for a specified custodian.",
     *      tags={"custodian"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="custodianId",
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
     *                          ref="#/components/schemas/Custodian",
     *                          @OA\Property(property="approvals", type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="SAIL Databank"),
     *                                  @OA\Property(property="contact_email", type="string", example="sail@email.com"),
     *                                  @OA\Property(property="enabled", type="boolean", example=true)
     *                              )
     *                          )
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
     *          response=404,
     *          description="Custodian not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          )
     *      )
     * )
     */
    public function getProjects(Request $request, int $custodianId): JsonResponse
    {
        $projects = Project::searchViaRequest()
          ->applySorting()
          ->with(['approvals', 'organisations', 'modelState.state'])
          ->filterByCommon()
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
     * @OA\Get(
     *      path="/api/v1/custodian/{custodianId}/users/{userId}/projects",
     *      summary="Return all custodian projects associated with a user",
     *      description="Fetch a list of custodians projects associated with a user, along with pagination details.",
     *      tags={"custodian"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="custodianId",
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
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="user not found")
     *          )
     *      )
     * )
     */
    public function getUserProjects(Request $request, int $custodianId, int $userId): JsonResponse
    {
        $user = User::with('registry')->find($userId);

        if ($user) {
            $projects = Project::searchViaRequest()
                ->applySorting()
                ->with(['organisations', 'modelState.state'])
                ->filterByCommon()
                ->whereHas('custodians', function ($query) use ($custodianId) {
                    $query->where('custodians.id', $custodianId);
                })
                ->whereHas('projectUsers', function ($query) use ($user) {
                    $query->where('user_digital_ident', $user->registry->digi_ident);
                })
                ->paginate((int)$this->getSystemConfig('PER_PAGE'));

            return $this->OKResponse($projects);
        }

        return $this->NotFoundResponse();
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodian/{custodianId}/organisations",
     *      summary="Return all custodian organisations with projects",
     *      description="Fetch a list of custodians organisations with projects, along with pagination details.",
     *      tags={"custodian"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="custodianId",
     *          in="path",
     *          description="The ID of the custodian whose organisations are to be retrieved",
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
     *                          ref="#/components/schemas/Organisation",
     *                          @OA\Property(property="project", type="array",
     *                              @OA\Items(
     *                                  ref="#/components/schemas/Project",
     *                              )
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string", example="http://localhost:8100/api/v1/custodians/1/organisations?page=1"),
     *                  @OA\Property(property="last_page_url", type="string", example="http://localhost:8100/api/v1/custodians/1/organisations?page=1"),
     *                  @OA\Property(property="next_page_url", type="string", example=null),
     *                  @OA\Property(property="prev_page_url", type="string", example=null)
     *              )
     *          )
     *      )
     * )
     */
    public function getOrganisations(Request $request, int $custodianId): JsonResponse
    {
        $results = Organisation::searchViaRequest()
            ->applySorting()
            ->with(['sroOfficer', 'projects' => function ($query) {
                $query->filterByState()
                    ->with("modelState.state");
            }])
            ->whereHas('projects.custodians', function ($query) use ($custodianId) {
                $query->where('custodians.id', $custodianId);
            })
            ->whereHas('projects', function ($query) {
                $query->filterByState();
            })->getOrganisationsProjects();

        return $this->OKResponse($this->paginateCollection($results));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{custodianId}/projects_users",
     *      summary="Return all users associated to all projects associated with a custodian",
     *      description="Fetch a list of users for all projects along with pagination details for a specified custodian.",
     *      tags={"custodian"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="custodianId",
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
     *                          @OA\Property(property="first_name", type="string", example="Dan"),
     *                          @OA\Property(property="last_name", type="string", example="Ackroyd"),
     *                          @OA\Property(property="user_id", type="int", example=10),
     *                          @OA\Property(property="digi_ident", type="string", example="$2y$12$3Pu4jwByHs7p2dGLecNtFO7v2B1ftGxMqAFD5AvmvFwlX.7CgD2pW"),
     *                          @OA\Property(property="registry_id", type="int", example=10),
     *                          @OA\Property(property="project_id", type="int", example=10),
     *                          @OA\Property(property="project_name", type="string", example="This is a project name"),
     *                          @OA\Property(property="project_role", type="string", example="Principal Investigator (PI)"),
     *                          @OA\Property(property="organisation_id", type="int", example=10),
     *                          @OA\Property(property="organisation_name", type="string", example="SAIL Databank"),
     *                          @OA\Property(property="model_state", type="object",
     *                              @OA\Property(property="state", type="object",
     *                                  @OA\Property(property="slug", type="string", example="registered")
     *                              )
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string", example="http://localhost:8100/api/v1/custodians/{custodianId}/projects_users?page=1"),
     *                  @OA\Property(property="last_page_url", type="string", example="http://localhost:8100/api/v1/custodians/{custodianId}/projects_users?page=1"),
     *                  @OA\Property(property="next_page_url", type="string", example=null),
     *                  @OA\Property(property="prev_page_url", type="string", example=null)
     *              )
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
    public function getProjectsUsers(Request $request, int $custodianId): JsonResponse
    {
        $results = DB::table('registries as r')
            ->join('project_has_users as phu', 'phu.user_digital_ident', '=', 'r.digi_ident')
            ->join('projects as p', 'p.id', '=', 'phu.project_id')
            ->join('project_has_custodians as phc', 'phc.project_id', '=', 'p.id')
            ->join('users as u', 'u.registry_id', '=', 'r.id')
            ->join('project_roles as pr', 'phu.project_id', '=', 'pr.id')
            ->join('registry_has_affiliations as rha', 'rha.registry_id', '=', 'r.id')
            ->leftJoin('affiliations as a', 'a.id', '=', 'rha.affiliation_id')
            ->whereNull('a.to')
            ->orWhere('a.to', '=', '')
            ->leftJoin('organisations as o', 'o.id', '=', 'a.organisation_id')
            ->where('phc.custodian_id', $custodianId)
            ->join('model_states as ms', function ($join) {
                $join->on('u.id', '=', 'ms.stateable_id')
                    ->where('ms.stateable_type', '=', 'App\Models\User');
            })
            ->join('states as s', 's.id', '=', 'ms.state_id')
            ->select(
                'u.first_name',
                'u.last_name',
                'u.id AS user_id',
                'r.digi_ident',
                'r.id AS registry_id',
                'p.id AS project_id',
                'p.title AS project_name',
                'pr.name AS project_role',
                'a.organisation_id AS organisation_id',
                'o.organisation_name',
                's.slug AS model_state_slug',
            )
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        // Ignore this phpstan error because it actually works fine.
        // phpstan doesn't like us iterating over a non-iterable LengthAwarePaginator.
        // Working around it would require way too much jiggery-pokery.
        // @phpstan-ignore foreach.nonIterable
        foreach ($results as $result) {
            $result->model_state = [
                "state" => [
                    "slug" => $result->model_state_slug
                ]
            ];
            unset($result->model_state_slug);
        }

        if ($results) {
            return response()->json([
                'message' => 'success',
                'data' => $results,
            ], 200);
        }

        return response()->json([
            'message' => 'not found',
            'data' => null,
        ], 404);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/custodians/{id}/rules",
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
     *          response=404,
     *          description="Custodian not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Custodian not found")
     *          )
     *      )
     * )
     */
    public function getRules(Request $request, int $custodianId): JsonResponse
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
     * @OA\Patch(
     *      path="/api/v1/custodians/{id}/rules",
     *      summary="Update rules for a specific custodian",
     *      description="Updates the list of rules associated with the given custodian ID by syncing provided rule IDs.",
     *      tags={"Custodians"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the custodian",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="rule_ids", type="array",
     *                  @OA\Items(type="integer"),
     *                  example={1,2,3}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully updated rules",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Rules updated successfully"),
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
     *          description="Invalid request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Invalid rule IDs provided")
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
    public function updateCustodianRules(Request $request, int $custodianId): JsonResponse
    {
        $validated = $request->validate([
            'rule_ids' => 'required|array',
            'rule_ids.*' => 'integer|exists:decision_models,id',
        ]);

        $custodian = Custodian::findOrFail($custodianId);
        if (!$custodian) {
            return response()->json(['message' => 'Custodian not found'], 404);
        }

        foreach ($validated['rule_ids'] as $r) {
            $chr = CustodianHasRule::updateOrCreate([
                'custodian_id' => $custodian->id,
                'rule_id' => $r,
            ]);
        }

        return response()->json([
            'message' => 'success',
            'data' => true
        ]);
    }

    //Hide from swagger docs
    public function invite(Request $request, int $id): JsonResponse
    {
        try {
            $custodian = Custodian::where('id', $id)->first();

            $unclaimedUser = RMC::createUnclaimedUser([
                'firstname' => '',
                'lastname' => '',
                'email' => $custodian['contact_email'],
                'user_group' => 'CUSTODIANS',
                'custodian_id' => $id
            ]);

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

    public function usersWithCustodianApprovals(Request $request, int $id): JsonResponse
    {
        try {
            $results = DB::select(
                "
                SELECT
                    u.id AS user_id,
                    GROUP_CONCAT(p.id) AS project_id
                FROM users u
                INNER JOIN user_has_custodian_approvals uhca
                    ON uhca.user_id = u.id
                    AND uhca.custodian_id = ?
                INNER JOIN registries r
                    ON r.id = u.registry_id
                INNER JOIN project_has_users phu
                    ON phu.user_digital_ident = r.digi_ident
                INNER JOIN projects p
                    ON phu.project_id = p.id
                INNER JOIN project_has_custodians phc 
                	ON phc.custodian_id  = uhca.custodian_id 
                	AND phc.project_id = p.id
                GROUP BY u.id;
                ",
                [
                    $id,
                ]
            );

            $users = [];

            foreach ($results as $u) {
                $tmpUser = User::where('id', $u->user_id)->first()->toArray();
                foreach (explode(',', $u->project_id) as $p) {
                    $tmpUser['projects'][] = Project::where('id', $p)->first();
                }

                $users[] = $tmpUser;
                unset($tmpUser);
            }

            return $this->OKResponse($users);
        } catch (Exception $e) {
            return $this->ErrorResponse();
        }
    }
}
