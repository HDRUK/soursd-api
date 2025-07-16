<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Traits\StateWorkflow;
use App\Traits\FilterManager;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 *
 *
 * @OA\Schema (
 *      schema="Affiliation",
 *      title="Affiliation",
 *      description="Affiliation model",
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
 *          example="2023-10-10T15:43:00Z"
 *      ),
 *      @OA\Property(property="organisation_id",
 *          type="integer",
 *          example=1,
 *          description="Organisational link"
 *      ),
 *      @OA\Property(property="member_id",
 *          type="string",
 *          example="325987-skdjfh283429-lkfsfdh",
 *          description="Member ID UUID"
 *      ),
 *      @OA\Property(property="relationship",
 *          type="string",
 *          example="employee",
 *          description="Textual representation of affiliation relationship"
 *      ),
 *      @OA\Property(property="from",
 *          type="string",
 *          example="2023-01-12",
 *          description="Date affiliation commenced"
 *      ),
 *      @OA\Property(property="to",
 *          type="string",
 *          example="2024-12-01",
 *          description="Date affiliation concluded"
 *      ),
 *      @OA\Property(property="department",
 *          type="string",
 *          example="Research & Development",
 *          description="Department worked during affiliation"
 *      ),
 *      @OA\Property(property="role",
 *          type="string",
 *          example="Principal Investigator (PI)",
 *          description="Role held during affiliation"
 *      ),
 *      @OA\Property(property="email",
 *          type="string",
 *          example="user@domain.com",
 *          description="Professional email held during affiliation"
 *      ),
 *      @OA\Property(property="ror",
 *          type="string",
 *          example="0hgyr56",
 *          description="The ROR.org identifier for this affiliation institute"
 *      ),
 *      @OA\Property(property="registry_id",
 *          type="integer",
 *          example=123,
 *          description="The Registry primary key associated with this affiliation"
 *      )
 * )
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $organisation_id
 * @property string $member_id
 * @property string|null $relationship
 * @property string|null $from
 * @property string|null $to
 * @property string|null $department
 * @property string|null $role
 * @property string|null $email
 * @property string|null $ror
 * @property int $registry_id
 * @property int|null $verdict_user_id
 * @property string|null $verdict_date_actioned
 * @property int|null $verdict_outcome
 * @property-read mixed $registry_affiliation_state
 * @property-read \App\Models\Organisation|null $organisation
 * @property-read \App\Models\Registry|null $registry
 * @method static \Database\Factories\AffiliationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereOrganisationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereRegistryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereRor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereVerdictDateActioned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereVerdictOutcome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Affiliation whereVerdictUserId($value)
 * @mixin \Eloquent
 */
class Affiliation extends Model
{
    use HasFactory;
    use StateWorkflow;
    use LogsActivity;
    use FilterManager;

    protected static array $transitions = [
        State::STATE_AFFILIATION_INVITED => [
            State::STATE_AFFILIATION_PENDING
        ],
        State::STATE_AFFILIATION_PENDING => [
            State::STATE_AFFILIATION_APPROVED,
            State::STATE_AFFILIATION_REJECTED,
            State::STATE_AFFILIATION_LEFT,
        ],
        State::STATE_AFFILIATION_APPROVED => [
            State::STATE_AFFILIATION_REJECTED,
            State::STATE_AFFILIATION_LEFT,
        ],
        State::STATE_AFFILIATION_REJECTED => [
            State::STATE_AFFILIATION_APPROVED
        ],
        State::STATE_AFFILIATION_LEFT => []
    ];

    public $table = 'affiliations';

    public $timestamps = true;

    protected $fillable = [
        'organisation_id',
        'member_id',
        'relationship',
        'from',
        'to',
        'department',
        'role',
        'email',
        'ror',
        'registry_id',
        'verdict_user_id',
        'verdict_date_actioned',
        'verdict_outcome',
    ];

    public static function getTransitions(): array
    {
        return static::$transitions;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('affiliation')
            ->dontSubmitEmptyLogs();
    }


    /**
     * Get the organisation related to the affiliation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organisation>
     */
    public function organisation()
    {
        return $this->belongsTo(
            Organisation::class,
            'organisation_id',
            'id'
        );
    }

    /**
     * Get the registry related to the affiliation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Registry>
     */
    public function registry()
    {
        return $this->belongsTo(
            Registry::class,
            'registry_id',
            'id'
        );
    }
}
