<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use Http;
use Auth;
use Exception;
use RegistryManagementController as RMC;
use App\Services\DecisionEvaluatorService as DES;
use Carbon\Carbon;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Jobs\OrganisationIDVT;
use App\Models\Project;
use App\Models\DebugLog;
use App\Models\Organisation;
use App\Models\Charity;
use App\Models\OrganisationHasDepartment;
use App\Models\User;
use App\Models\UserHasDepartments;
use App\Models\PendingInvite;
use App\Traits\CommonFunctions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use TriggerEmail;
use App\Http\Traits\Responses;
use App\Models\Affiliation;
use Illuminate\Support\Facades\Gate;

class OrganisationController extends Controller
{
    use CommonFunctions;
    use Responses;

    protected $decisionEvaluator = null;

    /**
     * @OA\Get(
     *      path="/api/v1/organisations",
     *      summary="Return a list of organisations",
     *      description="Return a list of organisations",
     *      tags={"organisation"},
     *      summary="organisation@index",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Organisation",
     *                  @OA\Property(property="charities", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example="1"),
     *                          @OA\Property(property="registration_id", type="string", example="1186569"),
     *                          @OA\Property(property="name", type="string", example="Health Pathways UK Charity"),
     *                          @OA\Property(property="website", type="string", example="https://www.website1.com/"),
     *                          @OA\Property(property="address_1", type="string", example="3 WATERHOUSE SQUARE"),
     *                          @OA\Property(property="address_2", type="string", example="138-142 HOLBORN"),
     *                          @OA\Property(property="town", type="string", example="LONDON"),
     *                          @OA\Property(property="county", type="string", example="GREATER LONDON"),
     *                          @OA\Property(property="country", type="string", example="UNITED KINGDOM"),
     *                          @OA\Property(property="postcode", type="string", example="EC1N 2SW"),
     *                      ),
     *                  ),
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
        $organisations = [];
        $this->decisionEvaluator = new DES($request);

        $custodianId = $request->get('custodian_id');

        if (!$custodianId) {
            $organisations = Organisation::searchViaRequest()
                ->filterByState()
                ->applySorting()
                ->with([
                    'departments',
                    'subsidiaries',
                    'permissions',
                    'files',
                    'charities',
                    'registries',
                    'registries.user',
                    'registries.user.permissions',
                    'delegates'
                ])
                ->filterWhen('has_delegates', function ($query, $hasDelegates) {
                    if ($hasDelegates) {
                        $query->whereHas('delegates');
                    } else {
                        $query->whereDoesntHave('delegates');
                    }
                })
                ->filterWhen('has_soursd_id', function ($query, $hasSoursdId) {
                    if ($hasSoursdId) {
                        $query->whereNot('organisation_unique_id', '')->whereNotNull('organisation_unique_id');
                    } else {
                        $query->where('organisation_unique_id', '')->orWhereNull('organisation_unique_id');
                    }
                })
                ->paginate((int)$this->getSystemConfig('PER_PAGE'));

            $evaluations = $this->decisionEvaluator->evaluate($organisations->items(), true);
            $organisations->setCollection($organisations->getCollection()->map(function ($organisation) use ($evaluations) {
                $organisation->evaluation = $evaluations[$organisation->id] ?? null;
                return $organisation;
            }));
        }

        return $this->OKResponse($organisations);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/organisations/{id}",
     *      summary="Return an organisations entry by ID",
     *      description="Return an organisations entry by ID",
     *      tags={"organisations"},
     *      summary="organisations@show",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="organisations entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="organisations entry ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/Organisation",
     *                  @OA\Property(property="charities", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example="1"),
     *                          @OA\Property(property="registration_id", type="string", example="1186569"),
     *                          @OA\Property(property="name", type="string", example="Health Pathways UK Charity"),
     *                          @OA\Property(property="website", type="string", example="https://www.website1.com/"),
     *                          @OA\Property(property="address_1", type="string", example="3 WATERHOUSE SQUARE"),
     *                          @OA\Property(property="address_2", type="string", example="138-142 HOLBORN"),
     *                          @OA\Property(property="town", type="string", example="LONDON"),
     *                          @OA\Property(property="county", type="string", example="GREATER LONDON"),
     *                          @OA\Property(property="country", type="string", example="UNITED KINGDOM"),
     *                          @OA\Property(property="postcode", type="string", example="EC1N 2SW"),
     *                      ),
     *                  ),
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
        $this->decisionEvaluator = new DES($request);

        $organisation = Organisation::with([
            'departments',
            'subsidiaries',
            'permissions',
            'ceExpiryEvidence',
            'cePlusExpiryEvidence',
            'isoExpiryEvidence',
            'dsptkExpiryEvidence',
            'charities',
            'registries',
            'registries.user',
            'registries.user.permissions',
            'sector'
        ])->findOrFail($id);
        if ($organisation) {
            return $this->OKResponseExtended($organisation, 'rules', $this->decisionEvaluator->evaluate($organisation));
        }

        throw new NotFoundException();
    }

    // No swagger, internal call
    public function pastProjects(Request $request, int $id): JsonResponse
    {
        $projects = Project::with('organisations')
            ->where('start_date', '<', Carbon::now())
            ->where('end_date', '<', Carbon::now())
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($projects);
    }

    // No swagger, internal call
    public function presentProjects(Request $request, int $id): JsonResponse
    {
        $projects = Project::with('organisations')
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($projects);
    }

    // No swagger, internal call
    public function futureProjects(Request $request, int $id): JsonResponse
    {
        $projects = Project::with('organisations')
            ->where('start_date', '>', Carbon::now())
            ->where('end_date', '>', Carbon::now())
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($projects);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/organisations/{id}/idvt",
     *      summary="Return an organisations idvt details by ID",
     *      description="Return an organisations idvt details by ID",
     *      tags={"organisations"},
     *      summary="organisations@idvt",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="organisations entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="organisations entry ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="idvt_result", type="boolean", example="true"),
     *                  @OA\Property(property="idvt_result_perc", type="number", example="80"),
     *                  @OA\Property(property="idvt_errors", type="object", example="{}"),
     *                  @OA\Property(property="idvt_completed_at", type="string", example="2024-02-04 12:01:00")
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
    public function idvt(Request $request, int $id): JsonResponse
    {
        $organisation = Organisation::findOrFail($id);

        if ($organisation) {
            return $this->OKResponse(
                [
                    'id' => $organisation->id,
                    'idvt_result' => $organisation->idvt_result,
                    'idvt_errors' => $organisation->idvt_errors,
                    'idvt_completed_at' => $organisation->idvt_completed_at,
                    'idvt_result_perc' => $organisation->idvt_result_perc
                ]
            );
        }

        return $this->NotFoundResponse();
    }

    /**
     * @OA\Post(
     *      path="/api/v1/organisations",
     *      summary="Create an organisations entry",
     *      description="Create a organisations entry",
     *      tags={"organisations"},
     *      summary="organisations@store",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="organisations definition",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Organisation",
     *              @OA\Property(property="departments", type="array",
     *                  @OA\Items(type="integer"),
     *              ),
     *              @OA\Property(property="charities", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="registration_id", type="string", example="1186569"),
     *                      @OA\Property(property="name", type="string", example="Health Pathways UK Charity"),
     *                      @OA\Property(property="website", type="string", example="https://www.website1.com/"),
     *                      @OA\Property(property="address_1", type="string", example="3 WATERHOUSE SQUARE"),
     *                      @OA\Property(property="address_2", type="string", example="138-142 HOLBORN"),
     *                      @OA\Property(property="town", type="string", example="LONDON"),
     *                      @OA\Property(property="county", type="string", example="GREATER LONDON"),
     *                      @OA\Property(property="country", type="string", example="UNITED KINGDOM"),
     *                      @OA\Property(property="postcode", type="string", example="EC1N 2SW"),
     *                  ),
     *              ),
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
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
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
        if (!Gate::allows('create', Organisation::class)) {
            return $this->ForbiddenResponse();
        }
        try {
            $input = $request->all();
            $organisation = Organisation::create([
                'organisation_name' => $input['organisation_name'],
                'address_1' => $input['address_1'],
                'address_2' => $input['address_2'],
                'town' => $input['town'],
                'county' => $input['county'],
                'country' => $input['country'],
                'postcode' => $input['postcode'],
                'lead_applicant_organisation_name' => $input['lead_applicant_organisation_name'],
                'lead_applicant_email' => $input['lead_applicant_email'],
                'organisation_unique_id' => $input['organisation_unique_id'],
                'applicant_names' => $input['applicant_names'],
                'funders_and_sponsors' => $input['funders_and_sponsors'],
                'sub_license_arrangements' => $input['sub_license_arrangements'],
                'verified' => $input['verified'],
                'companies_house_no' => $input['companies_house_no'],
                'sector_id' => $input['sector_id'],
                'dsptk_certified' => $input['dsptk_certified'],
                'dsptk_ods_code' => $input['dsptk_ods_code'],
                'dsptk_expiry_date' => $input['dsptk_expiry_date'],
                'iso_27001_certified' => $input['iso_27001_certified'],
                'iso_27001_certification_num' => $input['iso_27001_certification_num'],
                'iso_expiry_date' => $input['iso_expiry_date'],
                'ce_certified' => $input['ce_certified'],
                'ce_certification_num' => $input['ce_certification_num'],
                'ce_expiry_date' => $input['ce_expiry_date'],
                'ce_plus_certified' => $input['ce_plus_certified'],
                'ce_plus_certification_num' => $input['ce_plus_certification_num'],
                'ce_plus_expiry_date' => $input['ce_plus_expiry_date'],
                'ror_id' => $input['ror_id'],
                'website' => $input['website'],
                'smb_status' => $input['smb_status'],
                'organisation_size' => $input['organisation_size'],
            ]);

            if (isset($input['departments'])) {
                foreach ($input['departments'] as $dept) {
                    OrganisationHasDepartment::create([
                        'organisation_id' => $organisation->id,
                        'department_id' => $dept,
                    ]);
                }
            }

            if (isset($input['charities']) && is_array($input['charities'])) {
                foreach ($input['charities'] as $charityData) {
                    if (!isset($charityData['registration_id'])) {
                        continue;
                    }

                    $charity = Charity::firstOrCreate(
                        ['registration_id' => $charityData['registration_id']],
                        [
                            'name' => $charityData['name'] ?? 'Unknown Charity',
                            'website' => $charityData['website'] ?? null,
                            'address_1' => $charityData['address_1'] ?? null,
                            'address_2' => $charityData['address_2'] ?? null,
                            'town' => $charityData['town'] ?? null,
                            'county' => $charityData['county'] ?? null,
                            'country' => $charityData['country'] ?? null,
                            'postcode' => $charityData['postcode'] ?? null,
                        ]
                    );

                    $organisation->charities()->attach($charity->id);
                }
            }


            // Run automated IDVT
            if (!in_array(config('speedi.system.app_env'), ['testing', 'ci'])) {
                OrganisationIDVT::dispatchSync($organisation);
            }

            return $this->CreatedResponse($organisation->id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function inviteOrganisationSimple(Request $request): JsonResponse
    {
        $input = [
            'type' => 'ORGANISATION_INVITE_SIMPLE',
            'to' => -1,
            'address' => $request->get('lead_applicant_email'),
            'by' => Auth::user()->id,
            'identifier' => 'organisation_invite_new',
        ];

        TriggerEmail::spawnEmail($input);

        return $this->OKResponse(null);
    }

    public function storeUnclaimed(Request $request): JsonResponse
    {

        $input = [
            'type' => 'ORGANISATION_INVITE_SIMPLE',
            'to' => -1,
            'address' => $request->get('lead_applicant_email'),
            'by' => Auth::user()->id,
            'identifier' => 'organisation_invite_new',
        ];

        TriggerEmail::spawnEmail($input);

        try {
            $input = $request->all();
            $organisation = Organisation::create([
                'organisation_name' => $input['organisation_name'],
                'address_1' => '',
                'address_2' => '',
                'town' => '',
                'county' => '',
                'country' => '',
                'postcode' => '',
                'lead_applicant_organisation_name' => '',
                'lead_applicant_email' => $input['lead_applicant_email'],
                'organisation_unique_id' => '',
                'applicant_names' => '',
                'funders_and_sponsors' => '',
                'sub_license_arrangements' => '',
                'verified' => 0,
                'companies_house_no' => '',
                'sector_id' => 0,
                'dsptk_certified' => 0,
                'dsptk_ods_code' => '',
                'dsptk_expiry_date' => null,
                'iso_27001_certified' => 0,
                'iso_27001_certification_num' => '',
                'iso_expiry_date' => null,
                'ce_certified' => 0,
                'ce_certification_num' => '',
                'ce_expiry_date' => null,
                'ce_plus_certified' => 0,
                'ce_plus_certification_num' => '',
                'ce_plus_expiry_date' => null,
                'ror_id' => '',
                'website' => '',
                'smb_status' => 0,
                'organisation_size' => null,
                'unclaimed' => isset($input['unclaimed']) ? $input['unclaimed'] : 1
            ]);

            return $this->CreatedResponse($organisation->id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/organisations/{id}",
     *      summary="Update an organisations entry",
     *      description="Update a organisations entry",
     *      tags={"organisations"},
     *      summary="organisations@update",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="organisations entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="organisations entry ID",
     *         ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="organisations definition",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Organisation",
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
     *                  ref="#/components/schemas/Organisation",
     *                  @OA\Property(property="charities", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example="1"),
     *                          @OA\Property(property="registration_id", type="string", example="1186569"),
     *                          @OA\Property(property="name", type="string", example="Health Pathways UK Charity"),
     *                          @OA\Property(property="website", type="string", example="https://www.website1.com/"),
     *                          @OA\Property(property="address_1", type="string", example="3 WATERHOUSE SQUARE"),
     *                          @OA\Property(property="address_2", type="string", example="138-142 HOLBORN"),
     *                          @OA\Property(property="town", type="string", example="LONDON"),
     *                          @OA\Property(property="county", type="string", example="GREATER LONDON"),
     *                          @OA\Property(property="country", type="string", example="UNITED KINGDOM"),
     *                          @OA\Property(property="postcode", type="string", example="EC1N 2SW"),
     *                      ),
     *                 )
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
            $input = $request->only(app(Organisation::class)->getFillable());
            $org = Organisation::findOrFail($id);
            if (!Gate::allows('update', $org)) {
                return $this->ForbiddenResponse();
            }
            $org->update($input);

            if ($request->has('charities')) {
                $this->updateOrganisationCharities($id, $request->input('charities'));
            }

            return $this->OKResponse($org);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/organisations/{id}",
     *      summary="Delete an organisations entry from the system by ID",
     *      description="Delete an organisations entry from the system",
     *      tags={"organisations"},
     *      summary="organisations@destroy",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="organisations entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="organisations entry ID",
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
            $organisation = Organisation::findOrFail($id);

            if (!Gate::allows('delete', $organisation)) {
                return $this->ForbiddenResponse();
            }
            $organisation->delete();

            return response()->json([
                'message' => 'success',
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * No swagger, internal call
     */
    public function countCertifications(Request $request, int $id): JsonResponse
    {
        try {
            $counts = DB::table('organisations')
                ->select(DB::raw(
                    'dsptk_certified + ce_certified + iso_27001_certified as `count`'
                ))
                ->where('id', $id)
                ->get();

            if ($counts && count($counts) > 0) {
                return response()->json([
                    'message' => 'success',
                    'data' => $counts[0]->count,
                ], 200);
            }

            return response()->json([
                'message' => 'success',
                'data' => 0,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/organisations/{id}/projects",
     *      summary="Return an all projects associated with an organisation",
     *      description="Return an all projects associated with an organisation (i.e. data-custodian)",
     *      tags={"organisation"},
     *      summary="organisation@getProjects",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organisation ID",
     *         required=true,
     *         example="1",
     *
     *         @OA\Schema(
     *            type="integer",
     *            description="Organisation ID",
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
     *                  @OA\Property(property="registry_id", type="integer", example="1"),
     *                  @OA\Property(property="name", type="string", example="My First Research Project"),
     *                  @OA\Property(property="public_benefit", type="string", example="A public benefit statement"),
     *                  @OA\Property(property="runs_to", type="string", example="2026-02-04"),
     *                  @OA\Property(property="affiliate_id", type="integer", example="2"),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *           @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function getProjects(Request $request, int $organisationId): JsonResponse
    {
        $projects = Project::searchViaRequest()
            ->applySorting()
            ->with([
                'organisations' => function($query) use ($organisationId) {
                    $query->where('organisations.id', $organisationId);
                }, 
                'modelState.state',
                'custodianHasProjectOrganisation' => function($query) use ($organisationId) {
                    $query->whereHas('projectOrganisation', function($query2) use ($organisationId) {
                            $query2->where('organisation_id', $organisationId);
                        })
                    ->with('modelState.state');
                },
            ])
            ->whereHas('organisations', function ($query) use ($organisationId) {
                $query->where('organisations.id', $organisationId);
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
     *      path="/api/v1/organisations/{id}/users",
     *      summary="Return all users associated with an organisation",
     *      description="Return all users associated with an organisation",
     *      tags={"organisation"},
     *      summary="organisation@getUsers",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organisation ID",
     *         required=true,
     *         example="1",
     *
     *         @OA\Schema(
     *            type="integer",
     *            description="Organisation ID",
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="first_name", type="string", example="John"),
     *                          @OA\Property(property="last_name", type="string", example="Doe"),
     *                          @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                          @OA\Property(property="created_at", type="string", example="2023-06-01T12:00:00Z"),
     *                          @OA\Property(property="updated_at", type="string", example="2023-06-01T12:00:00Z")
     *                      )
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string"),
     *                  @OA\Property(property="from", type="integer"),
     *                  @OA\Property(property="last_page", type="integer"),
     *                  @OA\Property(property="last_page_url", type="string"),
     *                  @OA\Property(property="next_page_url", type="string"),
     *                  @OA\Property(property="path", type="string"),
     *                  @OA\Property(property="per_page", type="integer"),
     *                  @OA\Property(property="prev_page_url", type="string"),
     *                  @OA\Property(property="to", type="integer"),
     *                  @OA\Property(property="total", type="integer")
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function getUsers(Request $request, int $organisationId): JsonResponse
    {
        try {
            $org = Organisation::findOrFail($organisationId);
            if (!Gate::allows('viewDetailed', $org)) {
                return $this->ForbiddenResponse();
            }
            $users = User::searchViaRequest()
                ->with([
                    'permissions',
                    'registry',
                    'registry.files',
                    'pendingInvites',
                    'organisation',
                    'departments',
                    'registry.education',
                    'registry.trainings',
                ])->where('organisation_id', $organisationId)
                ->paginate((int)$this->getSystemConfig('PER_PAGE'));

            return response()->json([
                'message' => 'success',
                'data' => $users,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/organisations/{id}/delegates",
     *      summary="Return all delegates associated with an organisation",
     *      description="Return all delegates associated with an organisation",
     *      tags={"organisation"},
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organisation ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Organisation ID"
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="first_name", type="string", example="John"),
     *                      @OA\Property(property="last_name", type="string", example="Doe"),
     *                      @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                      @OA\Property(property="created_at", type="string", format="date-time", example="2023-06-01T12:00:00Z"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time", example="2023-06-01T12:00:00Z")
     *                  )
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          )
     *      )
     * )
     */
    public function getDelegates(Request $request, int $organisationId): JsonResponse
    {
        try {
            $org = Organisation::findOrFail($organisationId);
            if (!Gate::allows('viewDetailed', $org)) {
                return $this->ForbiddenResponse();
            }
            $delegates = User::with(["departments"])
                ->where('organisation_id', $organisationId)
                ->where('is_delegate', 1)
                ->get();

            return response()->json([
                'message' => 'success',
                'data' => $delegates,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * No swagger, internal call
     */
    public function countUsers(Request $request, int $id): JsonResponse
    {
        try {
            $count = Affiliation::where('organisation_id', $id)->count();

            if ($count && $count > 0) {
                return response()->json([
                    'message' => 'success',
                    'data' => $count,
                ], 200);
            }

            return response()->json([
                'message' => 'success',
                'data' => 0,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/organisations/{id}/invite_user",
     *      summary="Invites a user to org",
     *      description="Invites a user to org",
     *      tags={"organisations"},
     *      summary="organisations@invite_user",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="organisations entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="organisations entry ID",
     *         ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Invite definition",
     *          @OA\JsonContent(
     *              @OA\Property(property="last_name", type="string", example="Smith"),
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="email", type="string", example="someone@somewhere.com"),
     *              @OA\Property(property="is_delegate", type="integer", example="1"),
     *              @OA\Property(property="department_id", type="integer", example="1"),
     *              @OA\Property(property="role", type="string", example="admin"),
     *              @OA\Property(property="user_group", type="string", example="USERS"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="integer", example="1"),
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
    public function inviteUser(Request $request, int $id): JsonResponse
    {
        try {
            $input = $request->all();
            if (User::where("email", $input['email'])->exists()) {
                return $this->ConflictResponse();
            }

            $unclaimedUser = RMC::createUnclaimedUser([
                'firstname' => $input['first_name'],
                'lastname' => $input['last_name'],
                'email' => $input['email'],
                'organisation_id' => (isset($input['user_group']) && $input['user_group'] === User::GROUP_ORGANISATIONS) ? $id : 0,
                'is_delegate' => isset($input['is_delegate']) ? $input['is_delegate'] : 0,
                'user_group' => isset($input['user_group']) ? $input['user_group'] : 'USERS',
                'role' => isset($input['role']) ? $input['role'] : null,
            ]);

            if (isset($input['department_id']) && $input['department_id'] !== 0 && $input['department_id'] != null) {
                UserHasDepartments::create([
                    'user_id' => $unclaimedUser->id,
                    'department_id' => $request['department_id'],
                ]);
            };
            $email = [];
            if (isset($input['is_delegate'])) {
                $email = [
                    'type' => 'USER_DELEGATE',
                    'to' => $unclaimedUser->id,
                    'by' => $id,
                    'identifier' => 'delegate_invite'
                ];
            } else {
                $email = [
                    'type' => 'USER',
                    'to' => $unclaimedUser->id,
                    'by' => $id,
                    'identifier' => 'researcher_invite'
                ];

                Affiliation::create([
                    'organisation_id' => $id,
                    'member_id' => '',
                    'relationship' => '',
                    'from' => '',
                    'to' => '',
                    'department' => '',
                    'role' => '',
                    'email' => $input['email'],
                    'ror' => '',
                    'registry_id' => $unclaimedUser->registry_id,
                ]);
            }

            TriggerEmail::spawnEmail($email);

            return response()->json([
                'message' => 'success',
                'data' => $unclaimedUser->id,
            ], 201);
        } catch (Exception $e) {
            DebugLog::create([
                'class' => __CLASS__,
                'log' => $e,
            ]);

            throw $e;
        }
    }

    //Hide from swagger docs
    public function invite(Request $request, int $id): JsonResponse
    {
        try {
            $organisation = Organisation::where('id', $id)->first();
            if (!$organisation) {
                return response()->json([
                    'message' => 'Organisation not found.',
                ], 404);
            }
            $unclaimedUser = RMC::createUnclaimedUser([
                'firstname' => '',
                'lastname' => '',
                'email' => $organisation['lead_applicant_email'],
                'user_group' => User::GROUP_ORGANISATIONS,
                'organisation_id' => $id
            ]);

            if (!$unclaimedUser->unclaimed) {
                return response()->json([
                    'message' => 'Lead applicant has already claimed their account.',
                ], 400);
            }

            $input = [
                'type' => 'ORGANISATION',
                'to' => $organisation->id,
                'unclaimed_user_id' => $unclaimedUser->id,
                'by' => $id,
                'identifier' => 'organisation_invite'
            ];

            TriggerEmail::spawnEmail($input);

            return response()->json([
                'message' => 'success',
                'data' => $organisation,
            ], 201);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * No swagger, internal call
     */
    public function countPresentProjects(Request $request, int $id): JsonResponse
    {
        try {
            $projectCount = Project::whereHas('organisations', function ($query) use ($id) {
                $query->where('organisations.id', $id);
            })
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->count();

            return response()->json(
                ['data' => $projectCount],
                200
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * No swagger, internal call
     */
    public function countPastProjects(Request $request, int $id): JsonResponse
    {
        try {
            $projectCount = Project::whereHas('organisations', function ($query) use ($id) {
                $query->where('organisations.id', $id);
            })
                ->where('start_date', '<', Carbon::now())
                ->where('end_date', '<', Carbon::now())
                ->count();

            return response()->json(
                ['data' => $projectCount],
                200
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function updateOrganisationCharities(int $organisationId, array $charities)
    {
        $organisation = Organisation::findOrFail($organisationId);

        $organisation->charities()->detach();

        foreach ($charities as $charityData) {
            if (empty($charityData['registration_id'])) {
                continue;
            }

            $charityCriteria = ['registration_id' => $charityData['registration_id']];
            $charityValues = [
                'name' => $charityData['name'] ?? 'Unknown Charity',
                'website' => $charityData['website'] ?? null,
                'address_1' => $charityData['address_1'] ?? null,
                'address_2' => $charityData['address_2'] ?? null,
                'town' => $charityData['town'] ?? null,
                'county' => $charityData['county'] ?? null,
                'country' => $charityData['country'] ?? null,
                'postcode' => $charityData['postcode'] ?? null,
            ];

            $charity = Charity::updateOrCreate($charityCriteria, $charityValues);

            $organisation->charities()->attach($charity->id);
        }
    }

    public function validateRor(Request $request, string $ror): JsonResponse
    {
        $response = Http::get(config('speedi.system.ror_api_url') . '/' . $ror);
        if ($response->status() === 200) {
            $payload = $response->json();
            $response->close();

            return response()->json([
                'message' => 'success',
                'data' => $payload,
            ], 200);
        }

        return response()->json([
            'message' => 'failed',
            'data' => 'not found',
        ], 404);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/organisations/{id}/registries",
     *      summary="Get all registries for an organisation",
     *      description="Returns all registries associated with the specified organisation",
     *      tags={"organisations"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Organisation ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="show_pending",
     *          in="query",
     *          description="Include users with pending invitations (true/false)",
     *          required=false,
     *          @OA\Schema(
     *             type="boolean"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="current_page", type="integer"),
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="registry_id", type="integer"),
     *                          @OA\Property(property="organisation_id", type="integer")
     *                      )
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string"),
     *                  @OA\Property(property="from", type="integer"),
     *                  @OA\Property(property="last_page", type="integer"),
     *                  @OA\Property(property="last_page_url", type="string"),
     *                  @OA\Property(property="next_page_url", type="string"),
     *                  @OA\Property(property="path", type="string"),
     *                  @OA\Property(property="per_page", type="integer"),
     *                  @OA\Property(property="prev_page_url", type="string"),
     *                  @OA\Property(property="to", type="integer"),
     *                  @OA\Property(property="total", type="integer")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="No registries found for this organisation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No registries found for this organisation")
     *          )
     *      )
     * )
     */
    public function getRegistries(Request $request, int $id): JsonResponse
    {
        try {
            $showPending = $request->boolean("show_pending");
            $affiliationIds = Organisation::getCurrentAffiliations($id)->filterByState()->pluck('id');

            $users = User::searchViaRequest()
                ->applySorting()
                ->with([
                    'registry.affiliations' => function ($query) use ($id) {
                        $query->where('organisation_id', $id)->limit(1);
                    },
                    'registry.affiliations.modelState.state',
                    'modelState.state'
                ])
                ->whereHas('registry.affiliations', function ($query) use ($affiliationIds) {
                    $query->whereIn('id', $affiliationIds);
                })
                ->where(function ($query) use ($showPending, $id) {
                    if ($showPending) {
                        $pendingInviteUserIds = PendingInvite::where([
                            'organisation_id' => $id,
                            'status' => config('speedi.invite_status.PENDING')
                        ])->pluck('user_id');

                        $query->orWhereIn('id', $pendingInviteUserIds);
                    }
                })
                ->paginate((int)$this->getSystemConfig('PER_PAGE'));

            return response()->json([
                'message' => 'success',
                'data' => $users,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
