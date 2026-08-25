<?php

return [

    /*
        Default configuration for SOURSD API Deployment
    */
    'custodians' => [
        [
            'name' => 'SAIL Databank',
            'contact_email' => 'sail@email.com',
        ],
        [
            'name' => 'NHS England',
            'contact_email' => 'nhse@email.com',
        ],
    ],
    'invite_status' => [
        'PENDING' => 'PENDING',
        'COMPLETE' => 'COMPLETE',
    ],
    'system' => [
        // Application configuration
        'app_name' => env('APP_NAME', 'Safe People Registry'),
        'app_env' => env('APP_ENV', 'production'),
        'app_url' => env('APP_URL', 'http://soursd-api:8100'),
        'portal_url' => env('PORTAL_URL', 'http://localhost:3000'),
        'portal_path_invite' => env('PORTAL_PATH_INVITE', 'invite'),
        'support_email' => env('SUPPORT_EMAIL', 'enquiries@safepeopleregistry.org'),
        'invite_time_hours' => env('INVITE_TIME_HOURS', 24),
        'registry_salt_1' => env('REGISTRY_SALT_1'),
        'registry_salt_2' => env('REGISTRY_SALT_2'),
        'custodian_salt_1' => env('CUSTODIAN_SALT_1'),
        'custodian_salt_2' => env('CUSTODIAN_SALT_2'),
        // System configuration
        'idvt_supplier_secret_key' => env('IDVT_SUPPLIER_SECRET_KEY'),
        // Keycloak configuration
        'keycloak_realm' => env('KEYCLOAK_REALM', 'SOURSD'),
        'keycloak_base_url' => env('KEYCLOAK_BASE_URL'),
        'keycloak_client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
        'keycloak_client_id' => env('KEYCLOAK_CLIENT_ID'),
        // Service configuration
        'clam_av_service_url' => env('CLAM_AV_SERVICE_URL', 'http://clamav:3001/scan_file'),
        'idvt_org_scanner' => env('IDVT_ORG_SCANNER'),
        'idvt_comapnies_house_url' => env('IDVT_COMPANIES_HOUSE_URL'),
        'ons_acredited_researcher_list_url' => env('ONS_ACREDITED_RESEARCHER_LIST_URL'),
        'ons_acredited_researcher_list_page_url' => env('ONS_ACCREDITED_RESEARCHER_LIST_PAGE_URL'),
        'ons_column_start_index' => env('ONS_COLUMN_START_INDEX', 1),
        'ons_row_start_index' => env('ONS_ROW_START_INDEX', 6),
        'notifications_enabled' => env('NOTIFICATIONS_ENABLED', true),
        'orcid_url' => env('ORCID_URL'),
        'orcid_app_id' => env('ORCID_APP_ID'),
        'orcid_redirect_url' => env('ORCID_REDIRECT_URL'),
        'orcid_public_url' => env('ORCID_PUBLIC_URL', 'https://pub.orcid.org/'),
        'orcid_auth_url' => env('ORCID_AUTH_URL', 'https://orcid.org/'),
        'orcid_client_id' => env('ORCID_CLIENT_ID'),
        'orcid_client_secret' => env('ORCID_CLIENT_SECRET'),
        'rules_engine_project_token' => env('RULES_ENGINE_PROJECT_TOKEN'),
        'rules_engine_service' => env('RULES_ENGINE_SERVICE'),
        'rules_engine_project_id' => env('RULES_ENGINE_PROJECT_ID'),
        'rules_engine_document_id' => env('RULES_ENGINE_DOCUMENT_ID'),
        'gateway_api_url' => env('GATEWAY_API_URL'),
        'gateway_handoff_secret' => env('GATEWAY_HANDOFF_SECRET'),
        'scanning_filesystem_disk' => env('SCANNING_FILESYSTEM_DISK', 'local_scan'),
        'ror_api_url' => env('ROR_API_URL'),
        'registry_image_url' => env('REGISTRY_IMAGE_URL', 'https://storage.googleapis.com/hdruk-registry-assets/logo-horiz-colour.png'),
        'sponsor_project_path' => "/organisation/profile/projects/{id}/safe-project",
        'keycloak_redirect_uri' => env('PORTAL_URL', 'http://localhost:3000') . "/keycloak?redirect_path=",
        'otp_affiliation_validity_minutes' => env('OTP_AFFILIATION_VALIDITY_MINUTES', 60),
        'clamav_basic_auth_username' => env('CLAMAV_BASIC_AUTH_USERNAME', ''),
        'clamav_basic_auth_password' => env('CLAMAV_BASIC_AUTH_PASSWORD', ''),
        'training_expire_days' => env('TRAINING_EXPIRE_DAYS', 30),
        'sendgrid_host' => env('SENDGRID_HOST', 'https://api.sendgrid.com'),
        'sendgrid_api_key' => env('SENDGRID_API_KEY', 'sendgrid-api-key'),
    ],
];
