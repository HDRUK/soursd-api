<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="State",
 *     type="object",
 *     title="State",
 *     description="Model representing states",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Unique identifier for the state"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Registered",
 *         description="Name of the state"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         example="registered",
 *         description="Slug identifier for the state"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-25T12:00:00Z",
 *         description="Timestamp when the state was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-26T12:00:00Z",
 *         description="Timestamp when the state was last updated"
 *     )
 * )
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $slug
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class State extends Model
{
    use HasFactory;

    protected $table = 'states';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'slug',
    ];

    public const STATE_REGISTERED = 'registered';
    public const STATE_PENDING = 'pending';
    public const STATE_FORM_RECEIVED = 'form_received';
    public const STATE_VALIDATION_IN_PROGRESS = 'validation_in_progress';
    public const STATE_VALIDATION_COMPLETE = 'validation_complete';
    public const STATE_MORE_USER_INFO_REQ = 'more_user_info_req';
    public const STATE_MORE_ORG_INFO_REQ = 'more_org_info_req';
    public const STATE_ESCALATE_VALIDATION = 'escalate_validation';
    public const STATE_VALIDATED = 'validated';
    public const STATE_APPROVED = 'approved';
    public const STATE_PROJECT_PENDING = 'project_pending';
    public const STATE_PROJECT_COMPLETED = 'project_completed';
    public const STATE_PROJECT_APPROVED = 'project_approved';
    public const STATE_AFFILIATION_INVITED = 'affiliation_invited';
    public const STATE_AFFILIATION_PENDING = 'affiliation_pending';
    public const STATE_AFFILIATION_APPROVED = 'affiliation_approved'; // affiliated
    public const STATE_AFFILIATION_REJECTED = 'affiliation_rejected';

    public const STATE_PROJECT_IN_PROGRESS = 'project_in_progress';
    public const STATE_PROJECT_DECLINED_APPROVAL = 'project_declined_approval';

    public const STATE_AFFILIATION_LEFT = 'affiliation_left'; // left organisation

    public const STATE_MORE_USER_INFO_REQ_ESCALATION_MANAGER = 'more_user_info_req_escalation_manager'; // Escalation [to Validation Manager] 
    public const STATE_MORE_ORG_INFO_REQ_ESCALATION_MANAGER = 'more_org_info_req_escalation_manager'; // Escalation [to Validation Manager]

    public const STATE_MORE_USER_INFO_REQ_ESCALATION_COMMITTEE = 'more_user_info_req_escalation_committee'; // Escalation [to Validation Committee]';
    public const STATE_MORE_ORG_INFO_REQ_ESCALATION_COMMITTEE = 'more_org_info_req_escalation_committee'; // Escalation [to Validation Committee]';

    public const STATE_USER_VALIDATION_DECLINED = 'user_validation_declined'; // user validation declined
    public const STATE_ORG_VALIDATION_DECLINED = 'org_validation_declined'; // org validation declined

    public const STATE_USER_LEFT_PROJECT = 'user_left_project'; // user left project
    public const STATE_ORG_LEFT_PROJECT = 'org_left_project'; // org left project

    public const STATES = [
        self::STATE_REGISTERED,
        self::STATE_PENDING,
        self::STATE_FORM_RECEIVED,
        self::STATE_VALIDATION_IN_PROGRESS,
        self::STATE_VALIDATION_COMPLETE,
        self::STATE_MORE_USER_INFO_REQ,
        self::STATE_MORE_ORG_INFO_REQ,
        self::STATE_ESCALATE_VALIDATION,
        self::STATE_VALIDATED,
        self::STATE_PROJECT_APPROVED,
        self::STATE_PROJECT_COMPLETED,
        self::STATE_PROJECT_PENDING,
        self::STATE_AFFILIATION_INVITED,
        self::STATE_AFFILIATION_PENDING,
        self::STATE_AFFILIATION_APPROVED,
        self::STATE_AFFILIATION_REJECTED,

        self::STATE_PROJECT_IN_PROGRESS,
        self::STATE_PROJECT_DECLINED_APPROVAL,
        self::STATE_AFFILIATION_LEFT,
        self::STATE_MORE_USER_INFO_REQ_ESCALATION_MANAGER,
        self::STATE_MORE_ORG_INFO_REQ_ESCALATION_MANAGER,
        self::STATE_MORE_USER_INFO_REQ_ESCALATION_COMMITTEE,
        self::STATE_MORE_ORG_INFO_REQ_ESCALATION_COMMITTEE,
        self::STATE_USER_VALIDATION_DECLINED,
        self::STATE_ORG_VALIDATION_DECLINED,
        self::STATE_USER_LEFT_PROJECT,
        self::STATE_ORG_LEFT_PROJECT,
    ];
}
