<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Traits\SearchManager;
use App\Traits\SearchProject;
use App\Traits\StateWorkflow;
use App\Traits\FilterManager;

/**
 * App\Models\Project
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project searchViaRequest(array|null $input = null)
 */
/**
 * @OA\Schema(
 *      schema="Project",
 *      title="Project",
 *      description="Project model",
 *      @OA\Property(property="id",
 *          type="integer",
 *          example=1,
 *          description="Model primary key"
 *      ),
 *      @OA\Property(property="title",
 *          type="string",
 *          example="Project name"
 *      ),
 *      @OA\Property(property="unique_id",
 *          type="string",
 *          example="89AItHDuaqXsfgqOA85d"
 *      ),
 *      @OA\Property(property="lay_summary",
 *          type="string",
 *          example="This study aims to evaluate how digital mental health interventions (such as mobile apps for meditation, cognitive behavioral therapy, and mental health tracking) affect the mental health and well-being of young adults aged 18-30."
 *      ),
 *      @OA\Property(property="public_benefit",
 *          type="string",
 *          example="The findings from this research could lead to improved digital health interventions tailored to the mental health needs of young adults.",
 *          description="A unique identifier for Custodian's within SOURSD"
 *      ),
 *      @OA\Property(property="request_category_type",
 *          type="string",
 *          example="Health and Social Research"
 *      ),
 *      @OA\Property(property="technical_summary",
 *          type="string",
 *          example="This project involves analyzing anonymized, aggregated data from digital health applications used by young adults."
 *      ),
 *      @OA\Property(property="other_approval_commitees",
 *          type="string",
 *          example="This project requires approval from:  University Institutional Review Board (IRB) to ensure ethical considerations are met. Data Access Committee (DAC) from the app providers to secure permissions for using anonymized, aggregated data."
 *      ),
 *      @OA\Property(property="start_date",
 *          type="string",
 *          example="2023-10-10T15:03:00Z"
 *      ),
 *      @OA\Property(property="end_date",
 *          type="string",
 *          example="2024-10-10T15:03:00Z"
 *      )
 * )
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $unique_id
 * @property string $title
 * @property string|null $lay_summary
 * @property string|null $public_benefit
 * @property string|null $request_category_type
 * @property string|null $technical_summary
 * @property string|null $other_approval_committees
 * @property string|null $start_date
 * @property string|null $end_date
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Custodian> $custodians
 * @property-read int|null $custodians_count
 * @property-read \App\Models\ModelState|null $modelState
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organisation> $organisations
 * @property-read int|null $organisations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectHasUser> $projectUsers
 * @property-read int|null $project_users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project applySorting()
 * @method static \Database\Factories\ProjectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project filterByCommon()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project filterByState()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project filterWhen(string $filter, $callback)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project searchViaRequest()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereLaySummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereOtherApprovalCommittees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project wherePublicBenefit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereRequestCategoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTechnicalSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withoutTrashed()
 * @mixin \Eloquent
 */
class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SearchManager;
    use SearchProject;
    use StateWorkflow;
    use FilterManager;

    protected static array $transitions = [
        State::STATE_PROJECT_PENDING => [
            State::STATE_PROJECT_PENDING,
            State::STATE_PROJECT_APPROVED,
        ],

        State::STATE_PROJECT_APPROVED => [
            State::STATE_PROJECT_APPROVED,
            State::STATE_PROJECT_DECLINED_APPROVAL,
            State::STATE_PROJECT_IN_PROGRESS,
        ],

        State::STATE_PROJECT_IN_PROGRESS => [
            State::STATE_PROJECT_IN_PROGRESS,
            State::STATE_PROJECT_COMPLETED,
        ],

        State::STATE_PROJECT_DECLINED_APPROVAL => [
            State::STATE_PROJECT_APPROVED,
        ],

        State::STATE_PROJECT_COMPLETED => [
        ],
    ];

    protected $table = 'projects';

    public $timestamps = true;

    protected $fillable = [
        'unique_id',
        'title',
        'lay_summary',
        'public_benefit',
        'request_category_type',
        'technical_summary',
        'other_approval_committees',
        'start_date',
        'end_date',
    ];

    /**
     * Compiles a list of exposed searchable fields
     */
    protected static array $searchableColumns = [
        'title',
        'start_date',
        'end_date',
        'unique_id',
        'status',
    ];

    protected static array $sortableColumns = [
        'title',
    ];

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProjectHasUser>
     */
    public function projectUsers(): HasMany
    {
        return $this->hasMany(ProjectHasUser::class);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\ProjectDetail>
     */
    public function projectDetail(): HasOne
    {
        return $this->hasOne(ProjectDetail::class);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Organisation>
     */
    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(
            Organisation::class,
            'project_has_organisations'
        );
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Custodian>
     */
    public function custodians(): BelongsToMany
    {
        return $this->belongsToMany(
            Custodian::class,
            'project_has_custodians',
            'project_id',
            'custodian_id'
        );
    }

    public function modelState(): MorphOne
    {
        return $this->morphOne(ModelState::class, 'stateable');
    }

    public static function getTransitions(): array
    {
        return static::$transitions;
    }

    public function projectHasOrganisations()
    {
        return $this->hasMany(
            ProjectHasOrganisation::class,
            'project_id',    // FK on project_has_organisations
            'id'             // local key on projects
        );
    }

    /**
     * Shortcut: All of the raw pivot rows linking this Project ↔ Organisation ↔ Custodian
     */
    public function custodianHasProjectOrganisation()
    {
        return $this->hasManyThrough(
            CustodianHasProjectOrganisation::class,     // final model
            ProjectHasOrganisation::class,              // through model
            'project_id',                               // FK on project_has_organisations → projects.id
            'project_has_organisation_id',              // FK on custodian_has_project_organisation → project_has_organisations.id
            'id',                                       // local key on projects
            'id'                                        // local key on project_has_organisations
        );
    }
}
