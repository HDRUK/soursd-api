<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema (
 *      schema="CustodianModelConfig",
 *      title="CustodianModelConfig",
 *      description="CustodianModelConfig model",
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
 *          example="2023-10-10T18:03:00Z"
 *      ),
 *      @OA\Property(property="decision_model_id",
 *          type="integer",
 *          example=1
 *      ),
 *      @OA\Property(property="active",
 *          type="boolean",
 *          example="true"
 *      ),
 *      @OA\Property(property="custodian_id",
 *          type="integer",
 *          example=12
 *      )
 * )
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $decision_model_id
 * @property int $active
 * @property int $custodian_id
 * @property-read \App\Models\Custodian|null $custodian
 * @method static \Database\Factories\CustodianModelConfigFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig whereCustodianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig whereDecisionModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustodianModelConfig whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustodianModelConfig extends Model
{
    use HasFactory;

    protected $table = 'custodian_model_configs';

    public $timestamp = true;

    protected $fillable = [
        'decision_model_id',
        'active',
        'custodian_id',
    ];

    /**
     * Get the custodian
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Custodian>
     */
    public function custodian(): BelongsTo
    {
        return $this->belongsTo(Custodian::class, 'custodian_id', 'id');
    }
}
