<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchManager;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema (
 *      schema="SsoTenant",
 *      title="SsoTenant",
 *      description="An enterprise customer's SAML Identity Provider connection",
 *      @OA\Property(property="id",
 *          type="integer",
 *          example=1,
 *          description="Model primary key"
 *      ),
 *      @OA\Property(property="created_at",
 *          type="string",
 *          example="2026-08-28T09:00:00Z"
 *      ),
 *      @OA\Property(property="updated_at",
 *          type="string",
 *          example="2026-08-28T09:00:00Z"
 *      ),
 *      @OA\Property(property="name",
 *          type="string",
 *          example="Acme Corp"
 *      ),
 *      @OA\Property(property="idp_alias",
 *          type="string",
 *          example="acme-corp"
 *      ),
 *      @OA\Property(property="metadata_url",
 *          type="string",
 *          example="https://sso.acme.example/federationmetadata.xml"
 *      ),
 *      @OA\Property(property="entity_id",
 *          type="string",
 *          example="https://sso.acme.example/adfs/services/trust"
 *      ),
 *      @OA\Property(property="metadata_imported_at",
 *          type="string",
 *          example="2026-08-28T09:00:00Z"
 *      ),
 *      @OA\Property(property="enabled",
 *          type="bool",
 *          example="true"
 *      ),
 *      @OA\Property(property="sp_entity_id",
 *          type="string",
 *          example="https://keycloak.dev.hdruk.cloud/realms/SOURSD",
 *          description="Keycloak's own SP entity ID - null until approved. Register this as the SAML Identifier on the customer's IdP."
 *      ),
 *      @OA\Property(property="sp_acs_url",
 *          type="string",
 *          example="https://keycloak.dev.hdruk.cloud/realms/SOURSD/broker/acme-corp/endpoint",
 *          description="Keycloak's ACS/reply URL for this tenant - null until approved."
 *      ),
 *      @OA\Property(property="sp_metadata_url",
 *          type="string",
 *          example="https://keycloak.dev.hdruk.cloud/realms/SOURSD/broker/acme-corp/endpoint/descriptor",
 *          description="Downloadable SP metadata descriptor most IdPs can import directly - null until approved."
 *      )
 * )
 */
class SsoTenant extends Model
{
    use HasFactory;
    use SearchManager;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'sso_tenants';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'idp_alias',
        'metadata_url',
        'metadata_xml',
        'entity_id',
        'metadata_imported_at',
        'enabled',
        'status',
        'submitted_by_user_id',
        'rejected_reason',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'metadata_imported_at' => 'datetime',
    ];

    protected $hidden = [
        'metadata_xml',
    ];

    /**
     * The customer's own IdP admin needs these to register Keycloak as a
     * trusted Service Provider on their side - SAML trust is a mutual
     * handshake, and importMetadata() only ever pulls their metadata in,
     * never pushes Keycloak's back out. Only meaningful once approved
     * (idp_alias exists), so all three are null until then.
     */
    protected $appends = [
        'sp_entity_id',
        'sp_acs_url',
        'sp_metadata_url',
    ];

    public function getSpEntityIdAttribute(): ?string
    {
        if (!$this->idp_alias) {
            return null;
        }

        return config('speedi.system.keycloak_base_url') . '/realms/' . config('speedi.system.keycloak_realm');
    }

    public function getSpAcsUrlAttribute(): ?string
    {
        if (!$this->idp_alias) {
            return null;
        }

        return $this->getSpEntityIdAttribute() . '/broker/' . $this->idp_alias . '/endpoint';
    }

    public function getSpMetadataUrlAttribute(): ?string
    {
        if (!$this->idp_alias) {
            return null;
        }

        return $this->getSpAcsUrlAttribute() . '/descriptor';
    }

    protected static array $searchableColumns = [
        'name',
        'idp_alias',
        'status',
    ];

    protected static array $sortableColumns = [
        'name',
        'idp_alias',
        'enabled',
        'status',
    ];

    /**
     * @return HasMany<SsoTenantDomain, $this>
     */
    public function domains(): HasMany
    {
        return $this->hasMany(SsoTenantDomain::class, 'sso_tenant_id');
    }
}
