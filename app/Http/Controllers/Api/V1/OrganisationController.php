<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use Str;
use Http;
use Keycloak;
use Exception;
use TriggerEmail;
use Carbon\Carbon;
use App\Models\User;
use App\Models\State;
use App\Models\Charity;
use App\Models\Project;
use App\Models\DebugLog;
use App\Models\Affiliation;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\PendingInvite;
use App\Http\Traits\Responses;
use App\Jobs\OrganisationIDVT;
use App\Models\DecisionModelType;
use App\Traits\CommonFunctions;
use Illuminate\Http\JsonResponse;
use App\Models\UserHasDepartments;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\NotFoundException;
use App\Models\ProjectHasSponsorship;
use RegistryManagementController as RMC;
use App\Models\OrganisationHasDepartment;
use App\Http\Requests\Organisations\GetUser;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Organisations\GetProject;
use App\Models\CustodianHasProjectOrganisation;
use App\Http\Requests\Organisations\GetDelegate;
use App\Http\Requests\Organisations\GetRegistry;
use App\Http\Requests\Organisations\ResentInvite;
use App\Models\CustodianHasProjectHasSponsorship;
use App\Services\DecisionEvaluatorService as DES;
use App\Http\Requests\Organisations\GetCountUsers;
use App\Http\Requests\Organisations\GetPastProject;
use App\Http\Requests\Organisations\GetOrganisation;
use App\Http\Requests\Organisations\GetFutureProject;
use App\Http\Requests\Organisations\GetPresentProject;
use App\Http\Requests\Organisations\DeleteOrganisation;
use App\Http\Requests\Organisations\OrganisationInvite;
use App\Http\Requests\Organisations\UpdateOrganisation;
use App\Http\Requests\Organisations\GetCountPastProject;
use App\Http\Requests\Organisations\GetOrganisationIdvt;
use App\Notifications\Organisations\OrganisationApproved;
use App\Notifications\Organisations\OrganisationDelegates;
use App\Http\Requests\Organisations\GetCountCertifications;
use App\Http\Requests\Organisations\GetCountPresentProject;
use App\Http\Requests\Organisations\OrganisationInviteUser;
use App\Http\Requests\Organisations\OrganisationValidateRor;
use App\Http\Requests\Organisations\UpdateSponsorshipStatus;
use App\Traits\Notifications\NotificationOrganisationManager;
use App\Traits\OrganisationsProjectUtils;
use App\Notifications\Organisations\OrganisationUpdateProfile;
use App\Http\Requests\Organisations\OrganisationUpdateApprover;
use App\Http\Requests\Organisations\GetStatus;

class OrganisationController extends Controller
{
    use CommonFunctions;
    use OrganisationsProjectUtils;
    use Responses;
    use NotificationOrganisationManager;

    protected $decisionEvaluator = null;

    /**
     * @OA\Get(
     *      path="/api/v1/organisations",
     *      operationId="organisationIndex",
     *      x={"internal"="true"},
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
        $this->decisionEvaluator = new DES([DecisionModelType::ORG_VALIDATION_RULES]);

        $custodianId = $request->get('custodian_id');
        $perPage = $request->get('per_page');

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
                    'delegates',
                    'sroOfficer.invitedBy.custodian_user',
                    'modelState.state'
                ])
                ->filterWhen('has_delegates', function ($query, $hasDelegates) {
                    if ($hasDelegates) {
                        $query->whereHas('delegates');
                    } else {
                        $query->whereDoesntHave('delegates');
                    }
                })
                ->paginate($perPage ?? (int)$this->getSystemConfig('PER_PAGE'));

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
     *      operationId="organisationsShow",
     *      x={"internal"="true"},
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
    public function show(GetOrganisation $request, int $id): JsonResponse
    {
        $this->decisionEvaluator = new DES([DecisionModelType::ORG_VALIDATION_RULES]);

        $organisation = Organisation::with([
            'departments',
            'subsidiaries',
            'permissions',
            'ceExpiryEvidence',
            'cePlusExpiryEvidence',
            'isoExpiryEvidence',
            'dsptkExpiryEvidence',
            'icoExpiryEvidence',
            'charities',
            'registries',
            'registries.user',
            'registries.user.permissions',
            'sector',
            'files',
        ])->findOrFail($id);

        if ($organisation) {
            $organisation['rules'] = $this->decisionEvaluator->evaluate($organisation);
            return $this->OKResponse($organisation);
        }

        throw new NotFoundException();
    }

    // No swagger, internal call
    public function pastProjects(GetPastProject $request, int $id): JsonResponse
    {
        $projects = Project::with('organisations')
            ->where('start_date', '<', Carbon::now())
            ->where('end_date', '<', Carbon::now())
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($projects);
    }

    // No swagger, internal call
    public function presentProjects(GetPresentProject $request, int $id): JsonResponse
    {
        $projects = Project::with('organisations')
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->paginate((int)$this->getSystemConfig('PER_PAGE'));

        return $this->OKResponse($projects);
    }

    // No swagger, internal call
    public function futureProjects(GetFutureProject $request, int $id): JsonResponse
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
     *      operationId="organisationsIdvt",
     *      x={"internal"="true"},
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
    public function idvt(GetOrganisationIdvt $request, int $id): JsonResponse
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
     *      operationId="organisationsStore",
     *      x={"internal"="true"},
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
        if (!Gate::allows('admin')) {
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
                'dsptk_ods_code' => $input['dsptk_ods_code'],
                'dsptk_expiry_date' => $input['dsptk_expiry_date'],
                'iso_27001_certification_num' => $input['iso_27001_certification_num'],
                'iso_expiry_date' => $input['iso_expiry_date'],
                'ce_certification_num' => $input['ce_certification_num'],
                'ce_expiry_date' => $input['ce_expiry_date'],
                'ce_plus_certification_num' => $input['ce_plus_certification_num'],
                'ce_plus_expiry_date' => $input['ce_plus_expiry_date'],
                'ror_id' => $input['ror_id'],
                'website' => $input['website'],
                'smb_status' => $input['smb_status'],
                'organisation_size' => $input['organisation_size'],
                'sro_profile_uri' => $input['sro_profile_uri'] ?? null,
                'ods_id' => $input['ods_id'] ?? null,
                'dsptk_date_last_published' => $input['dsptk_date_last_published'] ?? null,
                'dsptk_status' => $input['dsptk_status'] ?? null,
                'ico_registration_id' => $input['ico_registration_id'] ?? null,
                'ico_date_registered' => $input['ico_date_registered'] ?? null,
                'ico_expiry_date' => $input['ico_expiry_date'] ?? null,
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

    public function createOrgWithUser(Request $request): JsonResponse
    {
        $input = $request->all();

        $orgExists = Organisation::where('lead_applicant_email', $input['lead_applicant_email'])->exists();

        if ($orgExists) {
            return $this->ConflictResponse();
        }

        try {
            $organisationsData = [
                'organisation_name' => $input['organisation_name'],
                'address_1' => '',
                'address_2' => '',
                'town' => '',
                'county' => '',
                'country' => '',
                'postcode' => '',
                'lead_applicant_organisation_name' => '',
                'lead_applicant_email' => $input['lead_applicant_email'],
                'applicant_names' => '',
                'funders_and_sponsors' => '',
                'sub_license_arrangements' => '',
                'verified' => 0,
                'companies_house_no' => '',
                'sector_id' => 0,
                'dsptk_ods_code' => '',
                'dsptk_expiry_date' => null,
                'iso_27001_certification_num' => '',
                'iso_expiry_date' => null,
                'ce_certification_num' => '',
                'ce_expiry_date' => null,
                'ce_plus_certification_num' => '',
                'ce_plus_expiry_date' => null,
                'ror_id' => '',
                'website' => '',
                'smb_status' => 0,
                'organisation_size' => null,
                'unclaimed' => $input['unclaimed'] ?? 1,
                'sro_profile_uri' => $input['sro_profile_uri'] ?? null,
                'organisation_unique_id' => Str::random(40),
                'ods_id' => null,
                'dsptk_status' => null,
                'dsptk_date_last_published' => null,
                'ico_registration_id' => null,
                'ico_date_registered' => null,
                'ico_expiry_date' => null,
            ];

            $organisation = Organisation::create($organisationsData);

            if ($organisationsData['unclaimed'] === 1) {
                $user = User::create([
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'email' => $input['lead_applicant_email'],
                    'user_group' => User::GROUP_ORGANISATIONS,
                    'is_org_admin' => 1,
                    'organisation_id' => $organisation->id,
                ]);

                return $this->CreatedResponse([
                    'user_id' => $user->id,
                    'organisation_id' => $organisation->id,
                ]);
            } else {
                $response = Keycloak::getUserInfo($request->headers->get('Authorization'));
                $payload = $response->json();

                $request->replace([
                    "organisation_id" => $organisation->id,
                    "is_org_admin" => 1
                ]);

                $user = RMC::createOrganisationUser($payload, $request);

                return $this->CreatedResponse([
                    'user_id' => $user['user_id'],
                    'organisation_id' => $organisation->id,
                ]);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function storeUnclaimed(Request $request): JsonResponse
    {
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
                'applicant_names' => '',
                'funders_and_sponsors' => '',
                'sub_license_arrangements' => '',
                'verified' => 0,
                'companies_house_no' => '',
                'sector_id' => 0,
                'dsptk_ods_code' => '',
                'dsptk_expiry_date' => null,
                'iso_27001_certification_num' => '',
                'iso_expiry_date' => null,
                'ce_certification_num' => '',
                'ce_expiry_date' => null,
                'ce_plus_certification_num' => '',
                'ce_plus_expiry_date' => null,
                'ror_id' => '',
                'website' => '',
                'smb_status' => 0,
                'organisation_size' => null,
                'unclaimed' => $input['unclaimed'] ?? 1,
                'sro_profile_uri' => $input['sro_profile_uri'] ?? null,
                'organisation_unique_id' => Str::random(40),
                'ods_id' => $input['ods_id'] ?? null,
                'dsptk_status' => $input['dsptk_status'] ?? null,
                'dsptk_date_last_published' => $input['dsptk_date_last_published'] ?? null,
                'ico_registration_id' => $input['ico_registration_id'] ?? null,
                'ico_date_registered' => $input['ico_date_registered'] ?? null,
                'ico_expiry_date' => $input['ico_expiry_date'] ?? null,
            ]);

            $organisation->setState(State::STATE_INVITED);

            return $this->CreatedResponse($organisation->id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/organisations/{id}",
     *      operationId="organisationsUpdate",
     *      x={"internal"="true"},
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
    public function update(UpdateOrganisation $request, int $id): JsonResponse
    {
        try {
            $input = $request->only(app(Organisation::class)->getFillable());
            $org = Organisation::findOrFail($id);

            if (!Gate::allows('update', $org)) {
                return $this->ForbiddenResponse();
            }

            $originalOrg = $org->getOriginal();

            // we need more discussion aroud this disable
            // if (!$org->system_approved && (!isset($input['system_approved']) || $input['system_approved'] === false)) {
            //     return $this->ForbiddenResponse();
            // }

            $isRegistering = isset($input['unclaimed']) && $input['unclaimed'] === 0 && $org->unclaimed;

            $org->update($input);

            if ($isRegistering) {

                $org->setState(State::STATE_ORGANISATION_REGISTERED);

                $this->updateAllCustodianHasProjectOrganisationStates($org, State::STATE_ORG_IN_PROGRESS);


                Affiliation::with(['registry.user'])
                    ->where('organisation_id', $id)
                    ->whereHas(
                        'registry.user',
                        fn ($q) =>
                        $q->where('unclaimed', false)
                    )->each(fn ($affiliation) => $affiliation->setState(State::STATE_AFFILIATION_ACCOUNT_IN_PROGRESS));

            }

            $loggedInUserId = $request->user()->id;
            $loggedInUser = User::where('id', $loggedInUserId)->first();

            if ($request->has('charities')) {
                $this->updateOrganisationCharities($id, $request->input('charities'));
            }

            if ($org->wasChanged()) {
                $this->sendNotificationOnUpdate($loggedInUser, $org, $org->getOriginal());
            }

            activity('organisation')
                ->causedBy(Auth::user())
                ->performedOn($org)
                ->withProperties([
                    'organisation_id'   => $org->id,
                    'organisation_name' => $org->organisation_name,
                    'attributes'        => $org->getChanges(),
                    'old'               => $originalOrg,
                ])
                ->event('updated')
                ->log('updated');

            return $this->OKResponse($org->refresh());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function sendNotificationOnUpdate($loggedInUser, $newOrgDetails, $oldOrgDetails)
    {
        $organisationId = $newOrgDetails->id;

        // organisation
        $users = User::where([
            'organisation_id' => $organisationId
        ])->get();
        Notification::send($users, new OrganisationUpdateProfile($loggedInUser, $newOrgDetails, $oldOrgDetails, 'organisation'));

        // data custodian
        $userCustodianIds = CustodianHasProjectOrganisation::query()
            ->whereHas('projectOrganisation', function ($query) use ($organisationId) {
                $query->where('organisation_id', $organisationId);
            })
            ->select(['custodian_id'])
            ->pluck('custodian_id')->toArray();

        if ($userCustodianIds) {
            $userCustodians = User::whereIn('custodian_user_id', $userCustodianIds)->get();
            Notification::send($userCustodians, new OrganisationUpdateProfile($loggedInUser, $newOrgDetails, $oldOrgDetails, 'custodian'));
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/organisations/{id}",
     *      operationId="organisationsDestroy",
     *      x={"internal"="true"},
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
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function destroy(DeleteOrganisation $request, int $id): JsonResponse
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
    public function countCertifications(GetCountCertifications $request, int $id): JsonResponse
    {
        try {
            $organisation = Organisation::findOrFail($id);

            $counts = 0;
            if ($organisation->dsptk_ods_code && $organisation->dsptk_expiry_date && $organisation->dsptk_expiry_date > Carbon::now()) {
                $counts++;
            }
            if ($organisation->ce_certification_num && $organisation->ce_expiry_date && $organisation->ce_expiry_date > Carbon::now()) {
                $counts++;
            }
            if ($organisation->ce_plus_certification_num && $organisation->ce_plus_expiry_date && $organisation->ce_plus_expiry_date > Carbon::now()) {
                $counts++;
            }
            if ($organisation->iso_27001_certification_num && $organisation->iso_expiry_date && $organisation->iso_expiry_date > Carbon::now()) {
                $counts++;
            }

            return response()->json([
                'message' => 'success',
                'data' => $counts,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/organisations/{id}/projects",
     *      operationId="organisationGetProjects",
     *      x={"internal"="true"},
     *      summary="Return an all projects associated with an organisation",
     *      description="Return an all projects associated with an organisation (i.e. data-custodian)",
     *      tags={"organisation"},
     *      summary="organisation@getProjects",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organisation ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Organisation ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
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
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *           @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *           @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function getProjects(GetProject $request, int $organisationId): JsonResponse
    {
        $projects = Project::searchViaRequest()
            ->applySorting()
            ->filterByCommon()
            ->filterByState()
            ->with([
                'organisations' => function ($query) use ($organisationId) {
                    $query->where('organisations.id', $organisationId);
                },
                'modelState.state',
                'custodianHasProjectOrganisation' => function ($query) use ($organisationId) {
                    $query->whereHas('projectOrganisation', function ($query2) use ($organisationId) {
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
     *      path="/api/v1/organisations/{id}/projects/sponsorships",
     *      operationId="organisationGetSponsorshipsProjects",
     *      x={"internal"="true"},
     *      summary="Return an all projects associated with an organisation with sponsorships",
     *      description="Return an all projects associated with an organisation with sponsorships (i.e. data-custodian)",
     *      tags={"organisation"},
     *      summary="organisation@getSponsorshipsProjects",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organisation ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Organisation ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
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
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *           @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *           @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function getSponsorshipsProjects(GetProject $request, int $organisationId): JsonResponse
    {
        $perPage = $request->get('per_page');

        $projects = Project::searchViaRequest()
            ->applySorting()
            ->filterByCommon()
            ->filterByState()
            ->with([
                'sponsors' => function ($query) use ($organisationId) {
                    $query->where('organisations.id', $organisationId);
                },
                'modelState.state',
                'custodianHasProjectSponsorships' => function ($query) use ($organisationId) {
                    $query->whereHas('projectHasSponsorships', function ($query2) use ($organisationId) {
                        $query2->where('sponsor_id', $organisationId);
                    })
                    ->with('modelState.state');
                },
            ])
            ->whereHas('sponsors', function ($query) use ($organisationId) {
                $query->where('organisations.id', $organisationId);
            })
            ->withCount('projectUsers')
            ->paginate($perPage ?? (int)$this->getSystemConfig('PER_PAGE'));

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
     *      operationId="organisationGetUsers",
     *      x={"internal"="true"},
     *      summary="Return all users associated with an organisation",
     *      description="Return all users associated with an organisation",
     *      tags={"organisation"},
     *      summary="organisation@getUsers",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organisation ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="Organisation ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
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
    public function getUsers(GetUser $request, int $organisationId): JsonResponse
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
     *      operationId="organisationGetDelegates",
     *      x={"internal"="true"},
     *      summary="Return all delegates associated with an organisation",
     *      description="Return all delegates associated with an organisation",
     *      tags={"organisation"},
     *      security={{"bearerAuth":{}}},
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
     *              @OA\Property(property="message", type="string", example="not found")
     *          )
     *      )
     * )
     */
    public function getDelegates(GetDelegate $request, int $organisationId): JsonResponse
    {
        try {
            $org = Organisation::findOrFail($organisationId);
            if (!Gate::allows('viewDetailed', $org)) {
                return $this->ForbiddenResponse();
            }
            $delegates = User::with('departments')
            ->select('users.*')
            ->selectSub(function ($query) use ($organisationId) {
                $query->from('pending_invites')
                    ->select('status')
                    ->whereColumn('pending_invites.user_id', 'users.id')
                    ->where('organisation_id', $organisationId)
                    ->limit(1);
            }, 'invite_status')
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
    public function countUsers(GetCountUsers $request, int $id): JsonResponse
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
     *      operationId="organisationsInviteUser",
     *      x={"internal"="true"},
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
     *              @OA\Property(property="from_custodian", type="bool", example="true"),
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
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="forbidden")
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
    public function inviteUser(OrganisationInviteUser $request, int $id): JsonResponse
    {
        try {
            $input = $request->all();
            if (User::where("email", $input['email'])->exists()) {
                return $this->ConflictResponse();
            }

            $loggedInUserId = $request->user()->id;
            $loggedInUser = User::where('id', $loggedInUserId)->first();
            if ($loggedInUser->user_group !== User::GROUP_ORGANISATIONS && $loggedInUser->user_group !== User::GROUP_ADMINS) {
                return $this->BadRequestResponse('Non-organisation users cannot invite members via this endpoint.');
            }

            $unclaimedUser = RMC::createUnclaimedUser([
                'firstname' => $input['first_name'],
                'lastname' => $input['last_name'],
                'email' => $input['email'],
                'organisation_id' => (isset($input['user_group']) && $input['user_group'] === User::GROUP_ORGANISATIONS) ? $id : 0,
                'is_delegate' => isset($input['is_delegate']) ? $input['is_delegate'] : 0,
                'user_group' => isset($input['user_group']) ? $input['user_group'] : 'USERS',
                'role' => isset($input['role']) ? $input['role'] : null,
                'invited_by' => $request->user()->id,
                'is_sro' => 0
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
                    'identifier' => 'delegate_invite',
                    'typeInvite' => 'delegate_invite',
                ];
            } else {
                $email = [
                    'type' => 'USER',
                    'to' => $unclaimedUser->id,
                    'by' => $id,
                    'identifier' => 'organisation_user_invite',
                    'typeInvite' => 'organisation_user_invite',
                ];

                $affiliation = Affiliation::create([
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

                $affiliation->setState(State::STATE_AFFILIATION_INVITED);
            }

            TriggerEmail::spawnEmail($email);

            if ($unclaimedUser->is_delegate) {
                $this->sendNotificationOnDelegate($loggedInUser, $unclaimedUser);
            }

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

    /**
     * @OA\Post(
     *      path="/api/v1/organisations/{id}/custodian_invite_user",
     *      operationId="organisationsCustodianInviteUser",
     *      x={"internal"="true"},
     *      summary="Invites a user to org",
     *      description="Invites a user to org",
     *      tags={"organisations"},
     *      summary="organisations@custodian_invite_user",
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
     *              @OA\Property(property="from_custodian", type="bool", example="true"),
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
    public function custodianInviteUser(OrganisationInviteUser $request, int $id): JsonResponse
    {
        try {
            $input = $request->all();
            if (User::where("email", $input['email'])->exists()) {
                return $this->ConflictResponse();
            }

            $loggedInUserId = $request->user()->id;
            $loggedInUser = User::where('id', $loggedInUserId)->first();
            if ($loggedInUser->user_group !== User::GROUP_CUSTODIANS) {
                return $this->BadRequestResponse('Non-custodian users cannot invite members via this endpoint.');
            }

            $userGroup = isset($input['user_group']) ? $input['user_group'] : 'USERS';
            $unclaimedUser = RMC::createUnclaimedUser([
                'firstname' => $input['first_name'],
                'lastname' => $input['last_name'],
                'email' => $input['email'],
                'organisation_id' => (isset($input['user_group']) && $input['user_group'] === User::GROUP_ORGANISATIONS) ? $id : 0,
                'is_delegate' => 0,
                'user_group' => $userGroup,
                'role' => isset($input['role']) ? $input['role'] : null,
                'invited_by' => $request->user()->id,
            ]);

            if (isset($input['department_id']) && $input['department_id'] !== 0 && $input['department_id'] != null) {
                UserHasDepartments::create([
                    'user_id' => $unclaimedUser->id,
                    'department_id' => $request['department_id'],
                ]);
            };

            $user = User::where('id', $unclaimedUser->id)->first();
            $user->setState(State::STATE_INVITED);

            $email = [
                'type' => 'USER',
                'to' => $unclaimedUser->id,
                'by' => $id,
                'identifier' => 'custodian_user_invite',
                'custodianId' => $loggedInUserId,
                'typeInvite' => 'custodian_user_invite',
            ];

            if ($userGroup === 'USERS') {
                $affiliation = Affiliation::create([
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
                $affiliation->setState(State::STATE_AFFILIATION_INVITED);
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
    public function invite(OrganisationInvite $request, int $id): JsonResponse
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
                'organisation_id' => $id,
                'invited_by' => $request->user()->id,
                'is_sro' => 1
            ]);

            if (!$unclaimedUser->unclaimed) {
                return response()->json([
                    'message' => 'Lead applicant has already claimed their account.',
                ], 400);
            }

            $loggedInUserId = $request->user()?->id;
            $loggedInUser = User::where('id', $loggedInUserId)->first();

            $input = [
                'type' => 'ORGANISATION',
                'to' => $organisation->id,
                'unclaimed_user_id' => $unclaimedUser->id,
                'by' => $id,
                'identifier' => 'organisation_invite',
                'userName' => $loggedInUser->name,
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

    //Hide from swagger docs
    public function resentInvite(ResentInvite $request, int $id)
    {
        try {
            $loggedInUserId = $request->user()?->id;
            $loggedInUser = User::where('id', $loggedInUserId)->first();

            $pendingInvites = PendingInvite::where([
                'organisation_id' => $id,
                'status' => PendingInvite::STATE_PENDING,
                'type' => 'organisation_invite'
            ])->first();

            if (is_null($pendingInvites)) {
                throw new Exception('Invite not found');
            }

            $input = [
                'type' => 'ORGANISATION',
                'to' => $id,
                'unclaimed_user_id' => $pendingInvites->user_id,
                'by' => $id,
                'identifier' => 'organisation_invite',
                'userName' => $loggedInUser->name,
                '$inviteId' => $pendingInvites->id,
            ];

            TriggerEmail::spawnEmail($input);

            return $this->OKResponse('Organisation invite resent.');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * No swagger, internal call
     */
    public function countPresentProjects(GetCountPresentProject $request, int $id): JsonResponse
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
    public function countPastProjects(GetCountPastProject $request, int $id): JsonResponse
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

    public function validateRor(OrganisationValidateRor $request, string $ror): JsonResponse
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
     *      operationId="organisationsGetRegistries",
     *      x={"internal"="true"},
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
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)")
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
    public function getRegistries(GetRegistry $request, int $id): JsonResponse
    {
        try {
            $showPending = $request->boolean("show_pending");
            $affiliationIds = Organisation::getCurrentAffiliations($id)->filterByState()->pluck('id');

            $users = User::searchViaRequest()
                ->applySorting()
                ->with([
                    'registry.affiliations' => function ($q) use ($affiliationIds, $id) {
                        $q->whereIn('id', $affiliationIds)
                          ->where('organisation_id', $id)->limit(1);
                    },
                    'registry.affiliations.modelState.state',
                    'modelState.state'
                ])
                ->whereHas('registry.affiliations', function ($query) use ($affiliationIds, $id) {
                    $query->whereIn('id', $affiliationIds)
                          ->where('organisation_id', $id);
                })
                ->where(function ($query) use ($showPending, $id) {
                    if ($showPending) {
                        $pendingInviteUserIds = PendingInvite::where([
                            'organisation_id' => $id,
                            'status' => PendingInvite::STATE_PENDING,
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


    /**
     * @OA\Get(
     *     path="/api/v1/organisations/{id}/status",
     *     operationId="organisationsGetStatus",
     *     x={"internal"="true"},
     *     summary="Get organisation status",
     *     description="Returns the organisation with its model state and state",
     *     tags={"organisations"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Organisation ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Example Organisation"),
     *
     *                 @OA\Property(
     *                     property="model_state",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *
     *                     @OA\Property(
     *                         property="state",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Active")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid argument(s)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid argument(s)")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Organisation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Organisation not found")
     *         )
     *     )
     * )
     */
    public function getStatus(GetStatus $request, int $id): JsonResponse
    {
        try {
            $organisation = Organisation::with('modelState.state')
            ->findOrFail($id);
            return $this->OKResponse($organisation->modelState);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/organisations/{id}/approved",
     *      operationId="organisationsUpdateApproved",
     *      x={"internal"="true"},
     *      summary="SuperAdmin update org system_approved flag",
     *      description="Updates the system_approved flag for an organisation",
     *      tags={"organisations"},
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
     *          description="System approval update definition",
     *          @OA\JsonContent(
     *              @OA\Property(property="system_approved", type="bool", example="true"),
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
    public function updateApproved(OrganisationUpdateApprover $request, int $id): JsonResponse
    {
        try {
            $input = $request->only(app(Organisation::class)->getGuarded());
            $org = Organisation::findOrFail($id);
            $originalOrg = $org->getOriginal();

            if (!Gate::allows('admin')) {
                return $this->ForbiddenResponse();
            }

            if ($org->system_approved) {
                return $this->NoContent();
            }

            if (!isset($input['system_approved'])) {
                return $this->BadRequestResponse();
            }

            $org->system_approved = $input['system_approved'];
            $org->system_approved_at = Carbon::now();
            $org->save();

            if (!$org->unclaimed) {
                if (!$input['system_approved']) {
                    $this->updateAllCustodianHasProjectOrganisationStates($org, State::STATE_SYSTEM_APPROVAL);
                } else {
                    $this->updateAllCustodianHasProjectOrganisationStates($org, State::STATE_PENDING);
                    $org->setState(State::STATE_PENDING);

                    Affiliation::with(['registry.user'])
                    ->where('organisation_id', $org->id)
                    ->whereHas(
                        'modelState.state',
                        fn ($q) =>
                        $q->where('slug', State::STATE_AFFILIATION_REVIEW)
                    )
                    ->whereHas(
                        'registry.user',
                        fn ($q) =>
                        $q->where('unclaimed', false)
                    )->each(fn ($affiliation) => $affiliation->setState(State::STATE_AFFILIATION_PENDING));
                }
            }

            // email
            $input = [
                'type' => 'ORGANISATION_CONFIRMATION_WITH_SUCCESS',
                'to' => $id,
                'by' => -1,
                'identifier' => 'organisation_confirmation_with_success'
            ];

            TriggerEmail::spawnEmail($input);

            // notification
            $usersToNotify = $this->getNotificationUsers($id);
            Notification::send($usersToNotify, new OrganisationApproved($org));

            $affiliations = Affiliation::where([
                'organisation_id' => $id,
                'is_verified' => 1,
            ])->get();
            foreach ($affiliations as $affiliation) {
                if ($affiliation->getState() === State::STATE_AFFILIATION_ACCOUNT_IN_PROGRESS) {
                    $affiliation->setState(State::STATE_AFFILIATION_PENDING);
                }
            }

            activity('organisation')
                ->causedBy(Auth::user())
                ->performedOn($org)
                ->withProperties([
                    'organisation_id'   => $org->id,
                    'organisation_name' => $org->organisation_name,
                    'attributes'        => $org->getChanges(),
                    'old'               => $originalOrg,
                ])
                ->event('updated')
                ->log('approved');

            return $this->OKResponse(Organisation::findOrFail($id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateSponsorshipStatuses(UpdateSponsorshipStatus $request, int $id)
    {
        $input = $request->all();
        $loggedInUserId = $request->user()->id;

        try {
            $projectId = $input['project_id'];
            $projectHasSponsorship = ProjectHasSponsorship::where([
                'project_id' => $projectId,
                'sponsor_id' => $id,
            ])->first();
            if (is_null($projectHasSponsorship)) {
                throw new Exception('The assigned sponsorship for the project was not found.');
            }

            $organisation = Organisation::find($id);
            $project = Project::find($projectId);

            $chphSponsorship = CustodianHasProjectHasSponsorship::where('project_has_sponsorship_id', $projectHasSponsorship->id)->first();
            $initState = $chphSponsorship->getState();

            if ($input['status'] === 'approved') {
                $chphSponsorship->setState(State::STATE_SPONSORSHIP_APPROVED);
                PendingInvite::where([
                    'organisation_id' => $id,
                    'project_id' => $projectId,
                    'type' => 'sponsorship_request',
                    'status' => PendingInvite::STATE_PENDING,
                ])->update([
                    'status' => PendingInvite::STATE_COMPLETE,
                ]);

                activity('sponsorship')
                    ->causedBy(Auth::user())
                    ->performedOn($organisation)
                    ->withProperties([
                        'id' => $id,
                        'organisation_name' => $organisation->organisation_name,
                        'organisation' => $organisation,
                        'project' => $project,
                        'status' => State::STATE_SPONSORSHIP_APPROVED,
                    ])
                    ->event('updated')
                    ->log(State::STATE_SPONSORSHIP_APPROVED);
            }

            if ($input['status'] === 'rejected') {
                $chphSponsorship->setState(State::STATE_SPONSORSHIP_REJECTED);
                PendingInvite::where([
                    'organisation_id' => $id,
                    'project_id' => $projectId,
                    'type' => 'sponsorship_request',
                    'status' => PendingInvite::STATE_PENDING,
                ])->update([
                    'status' => PendingInvite::STATE_COMPLETE,
                ]);

                activity('sponsorship')
                    ->causedBy(Auth::user())
                    ->performedOn($organisation)
                    ->withProperties([
                        'id' => $id,
                        'organisation_name' => $organisation->organisation_name,
                        'organisation' => $organisation,
                        'project' => $project,
                        'status' => State::STATE_SPONSORSHIP_REJECTED,
                    ])
                    ->event('updated')
                    ->log(State::STATE_SPONSORSHIP_REJECTED);
            }

            $finalState = $chphSponsorship->fresh()->getState();
            if ($finalState !== $initState) {
                $this->notifyOnProjectSponsorStateChange($loggedInUserId, $id, $projectId, $input['status']);

            }

            return $this->OKResponse($finalState);
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    private function getNotificationUsers(int $orgId)
    {
        return User::where("organisation_id", $orgId)->get();
    }

    private function sendNotificationOnDelegate($loggedInUser, $delegate)
    {
        $user = User::where('organisation_id', $delegate->organisation_id)
            ->where('id', '<>', $delegate->id)
            ->get();

        Notification::send($user, new OrganisationDelegates($loggedInUser, $delegate, 'add'));
    }

}
