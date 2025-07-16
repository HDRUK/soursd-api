<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Traits\SearchManager;
use App\Traits\ActionManager;
use App\Traits\StateWorkflow;
use App\Traits\FilterManager;

/**
 * App\Models\Organisation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation searchViaRequest(array|null $input = null)
 */
/**
 * @OA\Schema (
 *      schema="Organisation",
 *      title="Organisation",
 *      description="Organisation model",
 *      @OA\Property(property="id",
 *          type="integer",
 *          example=1,
 *          description="Model primary key"
 *      ),
 *      @OA\Property(property="created_at",
 *          type="string",
 *          example="2023-10-10T15:03:00Z"
 *      ),
 *      @OA\Property(property="updated_at",
 *          type="string",
 *          example="2023-10-10T16:03:00Z"
 *      ),
 *      @OA\Property(property="organisation_name",
 *          type="string",
 *          example="An Organisation Ltd"
 *      ),
 *      @OA\Property(property="address_1",
 *          type="string",
 *          example="123 Organisation Road"
 *      ),
 *      @OA\Property(property="address_2",
 *          type="string",
 *          example="Other address line"
 *      ),
 *      @OA\Property(property="town",
 *          type="string",
 *          example="Town"
 *      ),
 *      @OA\Property(property="county",
 *          type="string",
 *          example="County"
 *      ),
 *      @OA\Property(property="country",
 *          type="string",
 *          example="Country"
 *      ),
 *      @OA\Property(property="postcode",
 *          type="string",
 *          example="Po5t c0de"
 *      ),
 *      @OA\Property(property="lead_applicant_organisation_name",
 *          type="string",
 *          example="Lead Applicant Organisation"
 *      ),
 *      @OA\Property(property="lead_applicant_email",
 *          type="string",
 *          example="lead.applicant@email.com"
 *      ),
 *      @OA\Property(property="organisation_unique_id",
 *          type="string",
 *          example="ghyt843lgfk-akdgfskjh"
 *      ),
 *      @OA\Property(property="applicant_names",
 *          type="string",
 *          example="Applicant One, Applicant Two"
 *      ),
 *      @OA\Property(property="funders_and_sponsors",
 *          type="string",
 *          example="Funder Org. Sponsor Org"
 *      ),
 *      @OA\Property(property="sub_license_arrangements",
 *          type="string",
 *          example="Sub-license arrangements..."
 *      ),
 *      @OA\Property(property="verified",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(property="dsptk_ods_code",
 *          type="string",
 *          example="8HQ90"
 *      ),
 *      @OA\Property(property="dsptk_certified",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(property="dsptk_expiry_date",
 *          type="string",
 *          example="2026-12-01"
 *      ),
 *      @OA\Property(property="iso_27001_certified",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(property="iso_27001_certification_num",
 *          type="string",
 *          example="NUM1234"
 *      ),
 *      @OA\Property(property="iso_expiry_date",
 *          type="string",
 *          example="2026-12-01"
 *      ),
 *      @OA\Property(property="ce_certified",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(property="ce_certification_num",
 *          type="string",
 *          example="NUM1234"
 *      ),
 *      @OA\Property(property="ce_expiry_date",
 *          type="string",
 *          example="2026-12-01"
 *      ),
 *      @OA\Property(property="ce_plus_certified",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(property="ce_plus_certification_num",
 *          type="string",
 *          example="NUM1234"
 *      ),
 *      @OA\Property(property="ce_plus_expiry_date",
 *          type="string",
 *          example="2026-12-01"
 *      ),
 *      @OA\Property(property="idvt_result",
 *          type="integer",
 *          example=1
 *      ),
 *      @OA\Property(property="idvt_result_perc",
 *          type="integer",
 *          example=100
 *      ),
 *      @OA\Property(property="idvt_errors",
 *          type="string",
 *          example="Verification failed for XYZ reason"
 *      ),
 *      @OA\Property(property="idvt_completed_at",
 *          type="string",
 *          example="2023-10-10T16:03:00Z"
 *      ),
 *      @OA\Property(property="companies_house_no",
 *          type="string",
 *          example="10887014"
 *      ),
 *      @OA\Property(property="sector_id",
 *          type="integer",
 *          example=1
 *      ),
 *      @OA\Property(property="ror_id",
 *          type="string",
 *          example="02wnqcb97",
 *          description="ROR.org identification for Research Organisations"
 *      ),
 *      @OA\Property(property="website",
 *          type="string",
 *          example="https://yourdomain.com"
 *      ),
 *      @OA\Property(property="smb_status",
 *          type="boolean",
 *          example="false",
 *          description="Declaration of small/medium business"
 *      ),
 *      @OA\Property(property="organisation_size",
 *          type="integer",
 *          example="1",
 *          description="Organisation size. Integer denotes list index rather than absolute value"
 *      ),
 *      @OA\Property(property="unclaimed",
 *          type="boolean",
 *          example="false",
 *          description="Unclaimed"
 *      ),
 * )
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $organisation_name
 * @property string $address_1
 * @property string|null $address_2
 * @property string $town
 * @property string $county
 * @property string $country
 * @property string $postcode
 * @property string|null $lead_applicant_organisation_name
 * @property string|null $lead_applicant_email
 * @property string|null $password
 * @property string $organisation_unique_id
 * @property string|null $applicant_names
 * @property string|null $funders_and_sponsors
 * @property string|null $sub_license_arrangements
 * @property bool $verified
 * @property string|null $dsptk_ods_code
 * @property int $dsptk_certified
 * @property \Illuminate\Support\Carbon|null $dsptk_expiry_date
 * @property int|null $dsptk_expiry_evidence
 * @property bool $iso_27001_certified
 * @property bool $ce_certified
 * @property string|null $ce_certification_num
 * @property \Illuminate\Support\Carbon|null $ce_expiry_date
 * @property int|null $ce_expiry_evidence
 * @property int $ce_plus_certified
 * @property string|null $ce_plus_certification_num
 * @property \Illuminate\Support\Carbon|null $ce_plus_expiry_date
 * @property int|null $ce_plus_expiry_evidence
 * @property bool|null $idvt_result
 * @property float|null $idvt_result_perc
 * @property string|null $idvt_errors
 * @property string|null $idvt_completed_at
 * @property string $companies_house_no
 * @property int $sector_id
 * @property string|null $iso_27001_certification_num
 * @property \Illuminate\Support\Carbon|null $iso_expiry_date
 * @property int|null $iso_expiry_evidence
 * @property string|null $ror_id
 * @property string|null $website
 * @property int|null $smb_status
 * @property int|null $organisation_size
 * @property bool $unclaimed
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActionLog> $actionLogs
 * @property-read int|null $action_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Affiliation> $affiliations
 * @property-read int|null $affiliations_count
 * @property-read \App\Models\File|null $ceExpiryEvidence
 * @property-read \App\Models\File|null $cePlusExpiryEvidence
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Charity> $charities
 * @property-read int|null $charities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $delegates
 * @property-read int|null $delegates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Department> $departments
 * @property-read int|null $departments_count
 * @property-read \App\Models\File|null $dsptkExpiryEvidence
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read mixed $evaluation
 * @property-read \App\Models\File|null $isoExpiryEvidence
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $latestEvidence
 * @property-read int|null $latest_evidence_count
 * @property-read \App\Models\ModelState|null $modelState
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registry> $registries
 * @property-read int|null $registries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActionLog> $secondaryActionLogs
 * @property-read int|null $secondary_action_logs_count
 * @property-read \App\Models\Sector|null $sector
 * @property-read \App\Models\User|null $sroOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subsidiary> $subsidiaries
 * @property-read int|null $subsidiaries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActionLog> $tertiaryActionLogs
 * @property-read int|null $tertiary_action_logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation applySorting()
 * @method static \Database\Factories\OrganisationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation filterByState()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation filterWhen(string $filter, $callback)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation getCurrentAffiliations($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation getOrganisationsProjects()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation searchViaRequest()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereApplicantNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCeCertificationNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCeCertified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCeExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCeExpiryEvidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCePlusCertificationNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCePlusCertified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCePlusExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCePlusExpiryEvidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCompaniesHouseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereDsptkCertified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereDsptkExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereDsptkExpiryEvidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereDsptkOdsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereFundersAndSponsors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereIdvtCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereIdvtErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereIdvtResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereIdvtResultPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereIso27001CertificationNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereIso27001Certified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereIsoExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereIsoExpiryEvidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereLeadApplicantEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereLeadApplicantOrganisationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereOrganisationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereOrganisationSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereOrganisationUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereRorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereSectorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereSmbStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereSubLicenseArrangements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereTown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereUnclaimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereWebsite($value)
 * @mixin \Eloquent
 */
class Organisation extends Model
{
    use HasFactory;
    use SearchManager;
    use ActionManager;
    use StateWorkflow;
    use FilterManager;

    protected $table = 'organisations';

    public $timestamps = true;

    protected $fillable = [
        'organisation_name',
        'address_1',
        'address_2',
        'town',
        'county',
        'country',
        'postcode',
        'lead_applicant_organisation_name',
        'lead_applicant_email',
        'organisation_unique_id',
        'applicant_names',
        'funders_and_sponsors',
        'sub_license_arrangements',
        'verified',
        'dsptk_ods_code',
        'dsptk_certified',
        'dsptk_expiry_date',
        'dsptk_expiry_evidence',
        'iso_27001_certified',
        'iso_27001_certification_num',
        'iso_expiry_date',
        'iso_expiry_evidence',
        'ce_certified',
        'ce_certification_num',
        'ce_expiry_date',
        'ce_expiry_evidence',
        'ce_plus_certified',
        'ce_plus_certification_num',
        'ce_plus_expiry_date',
        'ce_plus_expiry_evidence',
        'idvt_result',
        'idvt_result_perc',
        'idvt_errors',
        'idvt_completed_at',
        'companies_house_no',
        'sector_id',
        'ror_id',
        'website',
        'smb_status',
        'organisation_size',
        'unclaimed'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'iso_27001_certified' => 'boolean',
        'ce_certified' => 'boolean',
        'idvt_result' => 'boolean',
        'unclaimed' => 'boolean',
        'ce_expiry_date' => 'date:Y-m-d',
        'ce_plus_expiry_date' => 'date:Y-m-d',
        'iso_expiry_date' => 'date:Y-m-d',
        'dsptk_expiry_date' => 'date:Y-m-d'
    ];

    protected static array $searchableColumns = [
        'organisation_name',
    ];

    protected static array $sortableColumns = [
        'organisation_name',
    ];

    protected $hidden = [];

    protected $appends = ['evaluation'];

    public const ACTION_NAME_ADDRESS_COMPLETED = 'name_address_completed';
    public const ACTION_DIGITAL_ID_COMPLETED = 'digital_identifiers_completed';
    public const ACTION_SECTOR_SIZE_COMPLETED = 'sector_size_completed';
    public const ACTION_ADD_SUBSIDIARY_COMPLETED = 'add_subsidiary_completed';
    public const ACTION_DATA_SECURITY_COMPLETED = 'data_security_completed';
    public const ACTION_ADD_SRO_COMPLETED = 'add_sro_completed';
    public const ACTION_AFFILIATE_EMPLOYEES_COMPLETED = 'affiliate_employees_completed';

    protected static array $defaultActions = [
        self::ACTION_NAME_ADDRESS_COMPLETED,
        self::ACTION_DIGITAL_ID_COMPLETED,
        self::ACTION_SECTOR_SIZE_COMPLETED,
        self::ACTION_ADD_SUBSIDIARY_COMPLETED,
        self::ACTION_DATA_SECURITY_COMPLETED,
        self::ACTION_ADD_SRO_COMPLETED,
        self::ACTION_AFFILIATE_EMPLOYEES_COMPLETED,
    ];

    public static function defaultValidationChecks(): array
    {
        return [
            [
                'name' => 'organisation_aligned_with_sde_network',
                'description' => 'Is the Organisation aligned with the SDE network?',
            ],
            [
                'name' => 'confidence_in_costs_and_profits_for_future_projects',
                'description' => 'Are we confident costs would be met and profits realised for future projects?',
            ],
        ];
    }


    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Permission>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'organisation_has_custodian_permissions'
        );
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\File>
     */
    public function files(): BelongsToMany
    {
        return $this->belongsToMany(
            File::class,
            'organisation_has_files'
        );
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Sector>
     */
    public function sector()
    {
        return $this->belongsTo(
            Sector::class,
        );
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\User>
     */
    public function sroOfficer(): HasOne
    {
        return $this->hasOne(
            User::class,
        )->where('is_sro', 1);
    }

    //Possible refactor candidate
    public function scopeGetOrganisationsProjects($query)
    {
        $results = array();
        $organisations = $query->get();

        foreach ($organisations as $organisation) {
            if (count($organisation->projects)) {
                foreach ($organisation->projects as $project) {
                    unset($organisation->projects);

                    array_push($results, array_merge($organisation->toArray(), [
                        'project' => $project
                    ]));
                }
            } else {
                array_push($results, $organisation->toArray());
            }
        }

        return $results;
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\File>
     */
    public function latestEvidence(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'organisation_has_files')
            ->where('status', File::FILE_STATUS_PROCESSED)
            ->whereRaw('updated_at = (SELECT MAX(updated_at) FROM files f WHERE f.type = files.type)');
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\File>
     */
    public function ceExpiryEvidence(): BelongsTo
    {
        return $this->belongsTo(File::class, 'ce_expiry_evidence');
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\File>
     */
    public function cePlusExpiryEvidence(): BelongsTo
    {
        return $this->belongsTo(File::class, 'ce_plus_expiry_evidence');
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\File>
     */
    public function isoExpiryEvidence(): BelongsTo
    {
        return $this->belongsTo(File::class, 'iso_expiry_evidence');
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\File>
     */
    public function dsptkExpiryEvidence(): BelongsTo
    {
        return $this->belongsTo(File::class, 'dsptk_expiry_evidence');
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Affiliation>
     */
    public function affiliations(): HasMany
    {
        return $this->hasMany(Affiliation::class, 'organisation_id');
    }


    public function scopeGetCurrentAffiliations($query, $id)
    {
        return Affiliation::where('organisation_id', $id)->where('to', '=', '')
            ->orWhereNull('to');
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Project>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_has_organisations',
        );
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Registry, \App\Models\Affiliation, static>
     */
    public function registries(): HasManyThrough
    {
        return $this->hasManyThrough(
            Registry::class,
            Affiliation::class,
            'organisation_id',
            'id',
            'id',
            'registry_id'
        )->distinct();
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Department>
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'organisation_has_departments'
        );
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Subsidiary>
     */
    public function subsidiaries(): BelongsToMany
    {
        return $this->belongsToMany(
            Subsidiary::class,
            'organisation_has_subsidiaries'
        );
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\User>
     */
    public function delegates(): HasMany
    {
        return $this->hasMany(
            User::class,
        )->where('is_delegate', 1);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Charity>
     */
    public function charities()
    {
        return $this->belongsToMany(Charity::class, 'organisation_has_charity');
    }

    public function getEvaluationAttribute()
    {
        return $this->attributes['evaluation'] ?? null;
    }
}
