<?php

// Catalog of identity providers that can be linked to a user's account via Keycloak
// identity brokering. Adding a new provider is a two-part job: register it as an
// Identity Provider in the Keycloak realm (client id/secret, scopes), then add an
// entry here with a matching 'key' so it shows up as linkable in the API/UI.

return [
    'providers' => [
        [
            'key' => 'orcid',
            'label' => 'ORCID',
            'description' => 'Verified researcher identifier — connects publications & funding records',
            'status' => 'active',
        ],
        [
            'key' => 'github',
            'label' => 'GitHub',
            'description' => 'Public code contributions — supports the "skilled people" check',
            'status' => 'active',
        ],
        [
            'key' => 'linkedin',
            'label' => 'LinkedIn',
            'description' => 'Professional history — supports affiliation & role claims',
            'status' => 'coming_soon',
        ],
        [
            'key' => 'google',
            'label' => 'Google',
            'description' => 'Personal account — useful as a recovery identity',
            'status' => 'coming_soon',
        ],
    ],
];
