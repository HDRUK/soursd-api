<?php

namespace App\Models;

use App\Traits\FilterManager;
use App\Traits\SearchManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\StateWorkflow;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @OA\Schema(
 *     schema="CustodianHasProjectUser",
 *     title="Custodian Has Project User",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="project_has_user_id", type="integer", example=1),
 *     @OA\Property(property="custodian_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="projectHasUser", ref="#/components/schemas/ProjectHasUser"),
 * )
 *
 * @property int $project_has_user_id
 * @property int $custodian_id
 * @property int $approved
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Custodian $custodian
 * @property-read \App\Models\ProjectHasUser $projectHasUser
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectUser whereCustodianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianHasProjectUser whereProjectUserId($value)
 * @mixin \Eloquent
 */

class CustodianHasProjectUser extends Model
{
    use StateWorkflow;
    use SearchManager;
    use FilterManager;

    protected static array $transitions = [
        State::STATE_FORM_RECEIVED => [
            State::STATE_VALIDATION_IN_PROGRESS,
            State::STATE_MORE_USER_INFO_REQ,
            State::STATE_USER_LEFT_PROJECT,
        ],
        State::STATE_VALIDATION_IN_PROGRESS => [
            State::STATE_VALIDATION_COMPLETE,
            State::STATE_MORE_USER_INFO_REQ_ESCALATION_MANAGER,
            State::STATE_MORE_USER_INFO_REQ_ESCALATION_COMMITTEE,
            State::STATE_USER_VALIDATION_DECLINED,
            State::STATE_VALIDATED,
            State::STATE_USER_LEFT_PROJECT,
        ],
        State::STATE_VALIDATION_COMPLETE => [
            State::STATE_MORE_USER_INFO_REQ_ESCALATION_MANAGER,
            State::STATE_MORE_USER_INFO_REQ_ESCALATION_COMMITTEE,
            State::STATE_USER_VALIDATION_DECLINED,
            State::STATE_VALIDATED,
            State::STATE_USER_LEFT_PROJECT,
        ],
        State::STATE_MORE_USER_INFO_REQ_ESCALATION_MANAGER => [
            State::STATE_MORE_USER_INFO_REQ_ESCALATION_COMMITTEE,
            State::STATE_USER_VALIDATION_DECLINED,
            State::STATE_VALIDATED,
            State::STATE_USER_LEFT_PROJECT,
        ],
        State::STATE_MORE_USER_INFO_REQ_ESCALATION_COMMITTEE => [
            State::STATE_MORE_USER_INFO_REQ_ESCALATION_MANAGER,
            State::STATE_USER_VALIDATION_DECLINED,
            State::STATE_VALIDATED,
            State::STATE_USER_LEFT_PROJECT,
        ],
        State::STATE_VALIDATED => [
            State::STATE_USER_LEFT_PROJECT,
        ],
        State::STATE_USER_VALIDATION_DECLINED => [
            State::STATE_VALIDATED,
        ],
        State::STATE_USER_LEFT_PROJECT => [],
    ];

    public static function getTransitions(): array
    {
        return static::$transitions;
    }

    protected static array  $searchableColumns = ['projects.title'];
    protected static array  $sortableColumns = ['projects.title'];

    protected $table = 'custodian_has_project_has_user';

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'project_has_user_id',
        'custodian_id',
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
                $model->setState(State::STATE_FORM_RECEIVED);
                $model->save();
            }
        });
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProjectHasUser>
     */
    public function projectHasUser(): BelongsTo
    {
        return $this->belongsTo(ProjectHasUser::class, 'project_has_user_id');
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Custodian>
     */
    public function custodian(): BelongsTo
    {
        return $this->belongsTo(Custodian::class, 'custodian_id');
    }
}
