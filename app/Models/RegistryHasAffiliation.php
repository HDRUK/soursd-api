<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Traits\StateWorkflow;

/**
 * @OA\Schema(
 *     schema="RegistryHasAffiliation",
 *     type="object",
 *     title="RegistryHasAffiliation",
 *     description="Pivot model representing the relationship between registries and affiliations",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Unique identifier for the registry-affiliation relationship"
 *     ),
 *     @OA\Property(
 *         property="registry_id",
 *         type="integer",
 *         example=42,
 *         description="ID of the registry"
 *     ),
 *     @OA\Property(
 *         property="affiliation_id",
 *         type="integer",
 *         example=24,
 *         description="ID of the affiliation"
 *     )
 * )
 *
 * @property int $id
 * @property int $registry_id
 * @property int $affiliation_id
 * @property-read \App\Models\Affiliation|null $affiliation
 * @property-read \App\Models\ModelState|null $modelState
 * @property-read \App\Models\Registry|null $registry
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasAffiliation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasAffiliation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasAffiliation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasAffiliation whereAffiliationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasAffiliation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasAffiliation whereRegistryId($value)
 * @mixin \Eloquent
 */
class RegistryHasAffiliation extends Model
{
    use HasFactory;
    use StateWorkflow;

    protected $table = 'registry_has_affiliations';

    public $timestamps = false;

    protected $fillable = [
        'registry_id',
        'affiliation_id',
    ];

    /**
     * Get the affiliation associated with this registry-affiliation relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Affiliation>
     */
    public function affiliation(): BelongsTo
    {
        return $this->belongsTo(Affiliation::class, 'affiliation_id');
    }

    /**
     * Get the registry associated with this registry-affiliation relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Registry>
     */
    public function registry(): BelongsTo
    {
        return $this->belongsTo(Registry::class, 'registry_id');
    }

    // /**
    //  * Get the model state associated with this registry-affiliation relationship.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\MorphOne<\App\Models\ModelState>
    //  */
    // public function modelState(): MorphOne
    // {
    //     return $this->morphOne(ModelState::class, 'stateable');
    // }
}
