<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
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
 */
class Affiliation extends Model
{
    use HasFactory;

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
    ];

    protected $appends = ['registryAffiliationState'];
    protected $hidden = ['registryHasAffiliations'];

    public function getRegistryAffiliationStateAttribute()
    {
        return optional($this->registryHasAffiliations->first())->getState();
    }

    /**
     * Get the organisation related to the affiliation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * Get the organisation related to the affiliation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function registryHasAffiliations()
    {
        return $this->hasMany(
            RegistryHasAffiliation::class,
            'affiliation_id'
        );
    }
}
