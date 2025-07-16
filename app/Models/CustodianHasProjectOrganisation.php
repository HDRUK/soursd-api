<?php

namespace App\Models;

use App\Traits\FilterManager;
use App\Traits\SearchManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Traits\StateWorkflow;

/**
 *  @OA\Schema(
 *     schema="CustodianHasProjectOrganisation",
 *     type="object",
 *     title="CustodianHasProjectOrganisation",
 *     description="Custodian approval status for a project organisation",
 *     required={"project_has_organisation_id", "custodian_id"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="project_has_organisation_id", type="integer", example=42, description="ID of the project organisation"),
 *     @OA\Property(property="custodian_id", type="integer", example=7, description="ID of the custodian"),
 *     @OA\Property(property="approved", type="boolean", nullable=true, example=true, description="Approval flag"),
 *     @OA\Property(property="comment", type="string", nullable=true, example="Approved after review", description="Optional comment"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-01T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-01T14:00:00Z"),
 *
 *     @OA\Property(
 *         property="projectOrganisation",
 *         ref="#/components/schemas/ProjectHasOrganisation"
 *     ),
 *     @OA\Property(
 *         property="custodian",
 *         ref="#/components/schemas/Custodian"
 *     )
 * )
 *
 * @property int $organisation_id
 * @property int $custodian_id
 * @property int $approved
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Custodian|null $custodian
 * @property-read \App\Models\Organisation|null $organisation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectOrganisation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectOrganisation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectOrganisation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectOrganisation whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectOrganisation whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectOrganisation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectOrganisation whereCustodianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectOrganisation whereOrganisationId($value)
 * @mixin \Eloquent
 */
class CustodianHasProjectOrganisation extends Model
{
    use HasFactory;
    use StateWorkflow;
    use SearchManager;
    use FilterManager;

    protected static array $transitions = [
        State::STATE_PENDING => [
            State::STATE_VALIDATION_IN_PROGRESS,
            State::STATE_MORE_ORG_INFO_REQ,
        ],
        State::STATE_VALIDATION_IN_PROGRESS => [
            State::STATE_VALIDATION_COMPLETE,
            State::STATE_MORE_ORG_INFO_REQ_ESCALATION_MANAGER,
            State::STATE_MORE_ORG_INFO_REQ_ESCALATION_COMMITTEE,
            State::STATE_ORG_VALIDATION_DECLINED,
            State::STATE_VALIDATED,
        ],
        State::STATE_VALIDATION_COMPLETE => [
            State::STATE_ORG_VALIDATION_DECLINED,
            State::STATE_VALIDATED,
        ],
        State::STATE_MORE_ORG_INFO_REQ_ESCALATION_MANAGER => [
            State::STATE_MORE_ORG_INFO_REQ_ESCALATION_COMMITTEE,
            State::STATE_ORG_VALIDATION_DECLINED,
            State::STATE_VALIDATED,
        ],
        State::STATE_MORE_ORG_INFO_REQ_ESCALATION_COMMITTEE => [
            State::STATE_MORE_ORG_INFO_REQ_ESCALATION_MANAGER,
            State::STATE_ORG_VALIDATION_DECLINED,
            State::STATE_VALIDATED,
        ],
        State::STATE_ORG_VALIDATION_DECLINED => [
            State::STATE_VALIDATED,
        ],
        State::STATE_VALIDATED => [
            State::STATE_ORG_LEFT_PROJECT,
        ],
        State::STATE_ORG_LEFT_PROJECT => [],
    ];

    public static function getTransitions(): array
    {
        return static::$transitions;
    }

    protected $table = 'custodian_has_project_has_organisation';

    public $timestamps = true;

    protected $fillable = [
        'project_has_organisation_id',
        'custodian_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static array  $searchableColumns = [
        'projects.title',
        'organisations.organisation_name'
    ];

    protected static array  $sortableColumns = [
        'projects.title',
        'organisations.organisation_name'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if (in_array(StateWorkflow::class, class_uses($model))) {
                $model->setState(State::STATE_PENDING);
                $model->save();
            }
        });
    }


    /**
     * Get the organisation associated with the approval.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProjectHasOrganisation>
     */
    public function projectOrganisation(): BelongsTo
    {
        return $this->belongsTo(ProjectHasOrganisation::class, 'project_has_organisation_id');
    }

    /**
     * Get the custodian associated with the approval.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Custodian>
     */
    public function custodian(): BelongsTo
    {
        return $this->belongsTo(Custodian::class, 'custodian_id');
    }
}
