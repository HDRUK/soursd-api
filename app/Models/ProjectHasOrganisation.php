<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *  @OA\Schema(
 *     schema="ProjectHasOrganisation",
 *     type="object",
 *     title="ProjectHasOrganisation",
 *     description="Relation between a project and an organisation",
 *     required={"project_id", "organisation_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="project_id", type="integer", example=123, description="ID of the related project"),
 *     @OA\Property(property="organisation_id", type="integer", example=456, description="ID of the related organisation"),
 *     @OA\Property(
 *         property="organisation",
 *         ref="#/components/schemas/Organisation"
 *     ),
 *     @OA\Property(
 *         property="project",
 *         ref="#/components/schemas/Project"
 *     )
 * )
 *
 * @property int $project_id
 * @property int $organisation_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectHasOrganisation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectHasOrganisation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectHasOrganisation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectHasOrganisation whereOrganisationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectHasOrganisation whereProjectId($value)
 * @mixin \Eloquent
 */
class ProjectHasOrganisation extends Model
{
    use HasFactory;

    protected $table = 'project_has_organisations';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'organisation_id',
    ];


    /**
     * Get the organisation associated with the approval.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organisation>
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    /**
     * Get the project associated with the approval.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Project>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function custodianHasProjectOrganisation()
    {
        return $this->hasMany(
            CustodianHasProjectOrganisation::class,
            'project_has_organisation_id',
            'id'
        );
    }
}
