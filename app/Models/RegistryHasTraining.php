<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="RegistryHasTraining",
 *     type="object",
 *     title="RegistryHasTraining",
 *     description="Pivot model representing the relationship between registries and trainings",
 *     @OA\Property(
 *         property="registry_id",
 *         type="integer",
 *         example=42,
 *         description="ID of the registry"
 *     ),
 *     @OA\Property(
 *         property="training_id",
 *         type="integer",
 *         example=24,
 *         description="ID of the training"
 *     )
 * )
 *
 * @property int $registry_id
 * @property int $training_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasTraining newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasTraining newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasTraining query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasTraining whereRegistryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistryHasTraining whereTrainingId($value)
 * @mixin \Eloquent
 */
class RegistryHasTraining extends Model
{
    use HasFactory;

    protected $table = 'registry_has_trainings';

    public $timestamps = false;

    protected $fillable = [
        'registry_id',
        'training_id',
    ];
}
