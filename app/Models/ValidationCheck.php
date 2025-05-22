<?php

namespace App\Models;

use App\Enums\ValidationCheckAppliesTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchManager;

/**
 * @OA\Schema(
 *     schema="ValidationCheck",
 *     type="object",
 *     title="Validation Check",
 *     required={"name", "description", "applies_to"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Check format"),
 *     @OA\Property(property="description", type="string", example="Ensures proper formatting of input"),
 *     @OA\Property(property="applies_to", type="string", example="user"),
 *     @OA\Property(property="enabled", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */
class ValidationCheck extends Model
{
    use HasFactory;
    use SearchManager;

    protected $fillable = [
        'name',
        'description',
        'applies_to',
        'enabled'
    ];

    protected static array $searchableColumns = [
        'applies_to',
        'name',
        'description',
    ];

    protected static array $sortableColumns = [
        'name',
        'description',
    ];

    protected $casts = [
        'applies_to' => ValidationCheckAppliesTo::class,
    ];

    public function custodians()
    {
        return $this->belongsToMany(Custodian::class, 'custodian_validation_check')
            ->withTimestamps();
    }

    public function scopeForContext($query, ValidationCheckAppliesTo $context)
    {
        return $query->where('applies_to', $context);
    }
}
