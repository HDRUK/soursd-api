<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema (
 *      schema="SsoTenantDomain",
 *      title="SsoTenantDomain",
 *      description="An email domain routed to a Registry SSO tenant's Identity Provider",
 *      @OA\Property(property="id",
 *          type="integer",
 *          example=1,
 *          description="Model primary key"
 *      ),
 *      @OA\Property(property="sso_tenant_id",
 *          type="integer",
 *          example=1
 *      ),
 *      @OA\Property(property="domain",
 *          type="string",
 *          example="acme.com"
 *      )
 * )
 */
class SsoTenantDomain extends Model
{
    use HasFactory;

    protected $table = 'sso_tenant_domains';

    public $timestamps = true;

    protected $fillable = [
        'sso_tenant_id',
        'domain',
    ];

    /**
     * @return BelongsTo<SsoTenant, $this>
     */
    public function ssoTenant(): BelongsTo
    {
        return $this->belongsTo(SsoTenant::class, 'sso_tenant_id');
    }
}
