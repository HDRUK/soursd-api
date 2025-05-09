<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\OrganisationHasCustodianApprovalObserver;
use App\Traits\ActionManager;

#[ObservedBy([OrganisationHasCustodianApprovalObserver::class])]
class OrganisationHasCustodianApproval extends Model
{
    use HasFactory;
    use ActionManager;

    protected $table = 'organisation_has_custodian_approvals';

    public $timestamps = false;

    protected $fillable = [
        'organisation_id',
        'custodian_id',
        'approved',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public const ORGANISATION_ALIGNED_SDE_NETWORK = 'organisation_aligned_sde_network';
    public const CONFIDENT_COSTS_FOR_PROJECTS = 'confident_cost_for_projects_will_be_met';

    protected static array $defaultActions = [
        self::ORGANISATION_ALIGNED_SDE_NETWORK,
        self::CONFIDENT_COSTS_FOR_PROJECTS
    ];

    /**
     * Get the organisation associated with the approval.
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    /**
     * Get the custodian associated with the approval.
     */
    public function custodian(): BelongsTo
    {
        return $this->belongsTo(Custodian::class, 'custodian_id');
    }
}
