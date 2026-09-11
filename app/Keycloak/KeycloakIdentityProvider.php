<?php

namespace App\Keycloak;

use Http;
use Exception;

/**
 * Provisions SAML Identity Providers against Keycloak's Admin REST API, so
 * enterprise SSO connections can be onboarded from Registry's own admin UI
 * instead of a developer configuring Keycloak's admin console per customer.
 *
 * Requires the service-account client (see Keycloak::getServiceToken()) to
 * additionally hold the `manage-identity-providers` realm-management role -
 * this is a manual, per-environment Keycloak admin console grant, not
 * something this codebase provisions.
 */
class KeycloakIdentityProvider extends Keycloak
{
    private static function realmAdminUrl(string $path): string
    {
        return config('speedi.system.keycloak_base_url') . '/admin/realms/' . config('speedi.system.keycloak_realm') . $path;
    }

    /**
     * Ask Keycloak to fetch and parse a customer's SAML metadata, either
     * from a URL or a raw uploaded XML document, into the config shape
     * `createIdentityProvider()` expects.
     */
    public static function importMetadata(?string $fromUrl, ?string $xml): array
    {
        $response = null;

        try {
            $request = Http::withHeaders([
                'Authorization' => self::getServiceToken(),
            ]);

            if ($fromUrl) {
                $response = $request->asJson()->post(
                    self::realmAdminUrl('/identity-provider/import-config'),
                    [
                        'providerId' => 'saml',
                        'fromUrl' => $fromUrl,
                    ]
                );
            } else {
                $response = $request->attach(
                    'file',
                    $xml,
                    'metadata.xml'
                )->post(
                    self::realmAdminUrl('/identity-provider/import-config'),
                    [
                        'providerId' => 'saml',
                    ]
                );
            }

            if (!$response->successful()) {
                throw new Exception('Failed to import SAML metadata: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            if ($response) {
                $response->close();
            }
        }
    }

    /**
     * Create the Keycloak Identity Provider itself from a previously
     * imported/parsed config.
     */
    public static function createIdentityProvider(string $alias, array $parsedConfig, string $displayName): void
    {
        $response = null;

        try {
            $response = Http::withHeaders([
                'Authorization' => self::getServiceToken(),
            ])->post(
                self::realmAdminUrl('/identity-provider/instances'),
                [
                    'alias' => $alias,
                    'providerId' => 'saml',
                    'displayName' => $displayName,
                    'enabled' => true,
                    'config' => $parsedConfig,
                ]
            );

            if (!$response->successful()) {
                throw new Exception('Failed to create identity provider: ' . $response->body());
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            if ($response) {
                $response->close();
            }
        }
    }

    /**
     * Attach the standard email/given_name/family_name attribute mappers to
     * a newly-created identity provider. Fixed for v1 - vendor-specific
     * claim-name mismatches (Okta vs Azure AD vs ADFS) are handled via a
     * direct Keycloak console tweak on that one tenant, not here.
     */
    public static function createAttributeMappers(string $alias): void
    {
        $mappers = [
            [
                'name' => 'email',
                'attribute' => 'urn:oid:0.9.2342.19200300.100.1.3',
                'userAttribute' => 'email',
            ],
            [
                'name' => 'given_name',
                'attribute' => 'urn:oid:2.5.4.42',
                'userAttribute' => 'firstName',
            ],
            [
                'name' => 'family_name',
                'attribute' => 'urn:oid:2.5.4.4',
                'userAttribute' => 'lastName',
            ],
        ];

        foreach ($mappers as $mapper) {
            $response = null;

            try {
                $response = Http::withHeaders([
                    'Authorization' => self::getServiceToken(),
                ])->post(
                    self::realmAdminUrl("/identity-provider/instances/{$alias}/mappers"),
                    [
                        'name' => $mapper['name'],
                        'identityProviderAlias' => $alias,
                        'identityProviderMapper' => 'saml-user-attribute-idp-mapper',
                        'config' => [
                            'attribute.name' => $mapper['attribute'],
                            'user.attribute' => $mapper['userAttribute'],
                        ],
                    ]
                );

                if (!$response->successful()) {
                    throw new Exception("Failed to create '{$mapper['name']}' mapper: " . $response->body());
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            } finally {
                if ($response) {
                    $response->close();
                }
            }
        }
    }

    /**
     * Re-import metadata (cert rotation) against an existing identity
     * provider, replacing its stored config.
     */
    public static function updateIdentityProviderConfig(string $alias, array $parsedConfig): void
    {
        $response = null;

        try {
            $response = Http::withHeaders([
                'Authorization' => self::getServiceToken(),
            ])->put(
                self::realmAdminUrl("/identity-provider/instances/{$alias}"),
                [
                    'alias' => $alias,
                    'providerId' => 'saml',
                    'config' => $parsedConfig,
                ]
            );

            if (!$response->successful()) {
                throw new Exception('Failed to update identity provider config: ' . $response->body());
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            if ($response) {
                $response->close();
            }
        }
    }

    /**
     * Toggle an identity provider on/off in Keycloak - used for the
     * disable/re-enable admin action. Immediate and two-sided: a disabled
     * tenant stops matching in domain lookup AND stops working in Keycloak
     * itself, so anyone mid-flow loses SSO right away.
     */
    public static function setIdentityProviderEnabled(string $alias, bool $enabled): void
    {
        $response = null;

        try {
            $response = Http::withHeaders([
                'Authorization' => self::getServiceToken(),
            ])->put(
                self::realmAdminUrl("/identity-provider/instances/{$alias}"),
                [
                    'alias' => $alias,
                    'providerId' => 'saml',
                    'enabled' => $enabled,
                ]
            );

            if (!$response->successful()) {
                throw new Exception('Failed to set identity provider enabled state: ' . $response->body());
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            if ($response) {
                $response->close();
            }
        }
    }

    /**
     * Removes an identity provider entirely. Only used to roll back a
     * `store()` attempt that succeeded in Keycloak but then failed to
     * persist locally - avoids leaving an orphaned IdP with no local
     * SsoTenant row.
     */
    public static function deleteIdentityProvider(string $alias): void
    {
        $response = null;

        try {
            $response = Http::withHeaders([
                'Authorization' => self::getServiceToken(),
            ])->delete(self::realmAdminUrl("/identity-provider/instances/{$alias}"));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            if ($response) {
                $response->close();
            }
        }
    }
}
