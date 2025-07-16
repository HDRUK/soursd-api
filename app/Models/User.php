<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\SearchManager;
use App\Traits\ActionManager;
use App\Traits\StateWorkflow;
use App\Traits\FilterManager;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @OA\Schema (
 *      schema="User",
 *      title="User",
 *      description="User model",
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="123"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="string",
 *          example="2024-02-04 12:00:00"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          example="2024-02-04 12:01:00"
 *      ),
 *      @OA\Property(
 *          property="first_name",
 *          type="string",
 *          example="A"
 *      ),
 *      @OA\Property(
 *          property="last_name",
 *          type="string",
 *          example="Researcher"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          example="person@somewhere.com"
 *      ),
 *      @OA\Property(
 *          property="email_verified_at",
 *          type="string",
 *          example="2024-02-04 12:00:00"
 *      ),
 *      @OA\Property(
 *          property="consent_scrape",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(
 *          property="public_opt_in",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(
 *          property="declaration_signed",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(
 *          property="organisation_id",
 *          type="integer",
 *          example="123"
 *      ),
 *      @OA\Property(
 *          property="orcid_scanning",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="orcid_scanning_completed_at",
 *          type="string",
 *          example="2024-02-04 12:01:00"
 *      ),
 *      @OA\Property(
 *          property="location",
 *          type="string",
 *          example="United Kingdom"
 *      ),
 *      @OA\Property(
 *          property="t_and_c_agreed",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(
 *          property="t_and_c_agreement_date",
 *          type="string",
 *          example="2024-02-04 12:00:00"
 *      )
 * )
 *
 * @property \Illuminate\Database\Eloquent\Collection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @property-read \App\Models\ModelState|null $modelState
 * @method \Illuminate\Notifications\DatabaseNotification[] unreadNotifications()
 * @property-read string $status
 * @property-read mixed $evaluation
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User searchViaRequest(array|null $input = null)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SearchManager;
    use ActionManager;
    use StateWorkflow;
    use FilterManager;
    use LogsActivity;

    public const GROUP_USERS = 'USERS';
    public const GROUP_ORGANISATIONS = 'ORGANISATIONS';
    public const GROUP_ADMINS = 'ADMINS';
    public const GROUP_CUSTODIANS = 'CUSTODIANS';

    public const GROUP_KC_USERS = '\Researchers';
    public const GROUP_KC_ORGANISATIONS = '\Organisations';
    public const GROUP_KC_CUSTODIANS = '\Custodians';
    public const GROUP_KC_ADMINS = '\Admins';

    public const STATUS_INVITED = 'invited';
    public const STATUS_REGISTERED = 'registered';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'registry_id',
        'provider',
        'keycloak_id',
        'user_group',
        'consent_scrape',
        'unclaimed',
        'feed_source',
        'public_opt_in',
        'declaration_signed',
        'organisation_id',
        'custodian_id',
        'custodian_user_id',
        'orc_id',
        'orcid_scanning',
        'orcid_scanning_completed_at',
        'is_delegate',
        'is_org_admin',
        'role',
        'location',
        't_and_c_agreed',
        't_and_c_agreement_date',
        'uksa_registered',
        'is_sro',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('user')
            ->dontSubmitEmptyLogs();
    }

    protected static array $searchableColumns = [
        'name',
        'first_name',
        'last_name',
        'email',
        'user_group',
        'registry_id'
    ];

    protected static array $sortableColumns = [
        'first_name',
        'last_name',
        'email',
    ];

    public const ACTION_PROFILE_COMPLETED = 'profile_completed';
    public const ACTION_AFFILIATIONS_COMPLETE = 'affiliations_complete';
    public const ACTION_TRAINING_COMPLETE = 'training_complete';
    public const ACTION_PROJECTS_REVIEW = 'projects_review';

    protected static array $defaultActions = [
        self::ACTION_PROFILE_COMPLETED,
        self::ACTION_AFFILIATIONS_COMPLETE,
        self::ACTION_TRAINING_COMPLETE,
        self::ACTION_PROJECTS_REVIEW
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'provider',
        'password',
        'keycloak_id',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'consent_scrape' => 'boolean',
        'orcid_scanning' => 'boolean',
        'uksa_registered' => 'boolean',
        'declaration_signed' => 'boolean',
        'is_sro' => 'boolean',
    ];

    protected $appends = ['status', 'evaluation'];

    public function getStatusAttribute(): string
    {
        return $this->unclaimed === 1 ? self::STATUS_INVITED : self::STATUS_REGISTERED;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Permission, \App\Models\User>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'user_has_custodian_permissions',
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Registry, \App\Models\User>
     */
    public function registry(): BelongsTo
    {
        return $this->belongsTo(
            Registry::class
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\PendingInvite, \App\Models\User>
     */
    public function pendingInvites(): HasMany
    {
        return $this->hasMany(PendingInvite::class);
    }

    /**
     * Get the organisation related to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organisation, \App\Models\User>
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(
            Organisation::class
        );
    }

    /**
     * Get the custodian related to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Custodian, \App\Models\User>
     */
    public function custodian(): BelongsTo
    {
        return $this->belongsTo(
            Custodian::class
        );
    }

    /**
     * Get the custodian user related to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CustodianUser, \App\Models\User>
     */
    public function custodian_user(): BelongsTo
    {
        return $this->belongsTo(
            CustodianUser::class
        );
    }

    /**
     * Get the organisation related to the affiliation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Department, \App\Models\User>
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'user_has_departments'
        );
    }

    public static function searchByEmail(string $email): \stdClass|null
    {
        $results = DB::select(
            '
            SELECT \'users\' as source, registry_id
            FROM users
            where email = ?

            UNION ALL
            
            SELECT \'affiliations\' as source, registry_id
            FROM affiliations
            WHERE email = ?
            ',
            [$email, $email]
        );

        $records = collect($results)->toArray();
        if (count($records)) {
            unset($results);
            return $records[0];
        }

        unset($results);
        return null;
    }

    public function getEvaluationAttribute()
    {
        return $this->attributes['evaluation'] ?? null;
    }

    public function scopeFromProject($query, $projectId)
    {
        return $query->whereHas('registry.projectUsers', function ($q) use ($projectId) {
            $q->where('project_id', $projectId);
        });
    }

    public function projectUsers()
    {
        return $this->hasManyThrough(
            ProjectHasUser::class,
            Registry::class,
            'id',                    // Registry.id (primary key)
            'user_digital_ident',    // ProjectHasRegistry.user_digital_ident
            'registry_id',           // User.registry_id (foreign key)
            'digi_ident'             // Registry.digi_ident
        );
    }

    public function scopeWithProjectMembership($query, $projectId)
    {
        return $query->withExists([
            'projectUsers as is_project_member' => function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            }
        ]);
    }

    public function actionLogs(): MorphMany
    {
        return $this->morphMany(ActionLog::class, 'entity');
    }

    public function isAdmin(): bool
    {
        return $this->user_group === self::GROUP_ADMINS;
    }

    public function inGroup(array $groups): bool
    {
        return in_array($this->user_group, $groups);
    }
}
