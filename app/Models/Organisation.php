<?php

namespace App\Models;

use App\Observers\OrganisationObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Traits\SearchManager;
use App\Traits\ActionManager;
use App\Traits\StateWorkflow;
use App\Traits\FilterManager;

/**
 * @OA\Schema(
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
 */
#[ObservedBy([OrganisationObserver::class])]
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

    protected $hidden = [
    ];

    protected $appends = [ 'evaluation' ];

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


    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'organisation_has_custodian_permissions'
        );
    }

    public function approvals(): BelongsToMany
    {
        return $this->belongsToMany(
            Custodian::class,
            'organisation_has_custodian_approvals'
        );
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(
            File::class,
            'organisation_has_files'
        );
    }

    public function sector()
    {
        return $this->belongsTo(
            Sector::class,
        );
    }

    public function sroOfficer(): HasOne
    {
        return $this->hasOne(
            User::class,
        )->where('is_sro', 1);
    }


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

    public function latestEvidence(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'organisation_has_files')
            ->where('status', File::FILE_STATUS_PROCESSED)
            ->whereRaw('updated_at = (SELECT MAX(updated_at) FROM files f WHERE f.type = files.type)');
    }

    public function ceExpiryEvidence(): BelongsTo
    {
        return $this->belongsTo(File::class, 'ce_expiry_evidence');
    }

    public function cePlusExpiryEvidence(): BelongsTo
    {
        return $this->belongsTo(File::class, 'ce_plus_expiry_evidence');
    }

    public function isoExpiryEvidence(): BelongsTo
    {
        return $this->belongsTo(File::class, 'iso_expiry_evidence');
    }

    public function dsptkExpiryEvidence(): BelongsTo
    {
        return $this->belongsTo(File::class, 'dsptk_expiry_evidence');
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(Affiliation::class, 'organisation_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_has_organisations',
        );
    }

    public function registries(): HasManyThrough
    {
        return $this->hasManyThrough(
            Registry::class,
            RegistryHasAffiliation::class,
            'affiliation_id',
            'id',
            'id',
            'registry_id'
        )->distinct();
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'organisation_has_departments'
        );
    }

    public function subsidiaries(): BelongsToMany
    {
        return $this->belongsToMany(
            Subsidiary::class,
            'organisation_has_subsidiaries'
        );
    }

    public function delegates(): HasMany
    {
        return $this->hasMany(
            User::class,
        )->where('is_delegate', 1);
    }

    public function charities()
    {
        return $this->belongsToMany(Charity::class, 'organisation_has_charity');
    }

    public function modelState(): MorphOne
    {
        return $this->morphOne(ModelState::class, 'stateable');
    }

    public function getEvaluationAttribute()
    {
        return $this->attributes['evaluation'] ?? null;
    }
}
