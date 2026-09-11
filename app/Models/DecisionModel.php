<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @OA\Schema(
 *     schema="DecisionModel",
 *     type="object",
 *     title="DecisionModel",
 *     description="Model representing decision models",
 *     required={"model_type", "conditions", "rule_class"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Unique identifier for the decision model"
 *     ),
 *     @OA\Property(
 *         property="model_type",
 *         type="string",
 *         example="App\Models\User",
 *         description="Type of the model associated with the decision"
 *     ),
 *     @OA\Property(
 *         property="conditions",
 *         type="string",
 *         example="status=active",
 *         description="Conditions for the decision model"
 *     ),
 *     @OA\Property(
 *         property="rule_class",
 *         type="string",
 *         example="App\Rules\CustomRule",
 *         description="Class defining the rules for the decision model"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Decision model for user validation",
 *         description="Description of the decision model"
 *     ),
 *     @OA\Property(
 *         property="decision_model_type_id",
 *         type="integer",
 *         example=42,
 *         description="ID of the decision model type associated with the decision"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-25T12:00:00Z",
 *         description="Timestamp when the decision model was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-26T12:00:00Z",
 *         description="Timestamp when the decision model was last updated"
 *     )
 * )
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $model_type
 * @property string $conditions
 * @property string $rule_class
 * @property string|null $description
 * @property int $decision_model_type_id
 * @property-read \App\Models\CustodianModelConfig|null $custodianModelConfig
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel whereDecisionModelTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel whereRuleClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DecisionModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DecisionModel extends Model
{
    use HasFactory;

    protected $table = 'decision_models';

    public $timestamps = true;

    protected $fillable = [
        'model_type',
        'conditions',
        'rule_class',
        'description',
        'decision_model_type_id',
    ];

    /**
     * Get the custodian model configuration associated with this decision model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\CustodianModelConfig>
     */
    public function custodianModelConfig(): HasOne
    {
        return $this->hasOne(CustodianModelConfig::class);
    }
}
