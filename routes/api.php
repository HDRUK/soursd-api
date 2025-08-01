<?php

use App\Http\Controllers\Api\V1\AccreditationController;
use App\Http\Controllers\Api\V1\AffiliationController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EndorsementController;
use App\Http\Controllers\Api\V1\ExperienceController;
use App\Http\Controllers\Api\V1\FileUploadController;
use App\Http\Controllers\Api\V1\HistoryController;
use App\Http\Controllers\Api\V1\IdentityController;
use App\Http\Controllers\Api\V1\InfringementController;
use App\Http\Controllers\Api\V1\CustodianController;
use App\Http\Controllers\Api\V1\CustodianUserController;
use App\Http\Controllers\Api\V1\ONSSubmissionController;
use App\Http\Controllers\Api\V1\OrganisationController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\QueryController;
use App\Http\Controllers\Api\V1\RegistryController;
use App\Http\Controllers\Api\V1\RegistryReadRequestController;
use App\Http\Controllers\Api\V1\SystemConfigController;
use App\Http\Controllers\Api\V1\TrainingController;
use App\Http\Controllers\Api\V1\TriggerEmailController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ResolutionController;
use App\Http\Controllers\Api\V1\EducationController;
use App\Http\Controllers\Api\V1\EmailTemplateController;
use App\Http\Controllers\Api\V1\SectorController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ActionLogController;
use App\Http\Controllers\Api\V1\CustodianHasProjectOrganisationController;
use App\Http\Controllers\Api\V1\ValidationCheckController;
use App\Http\Controllers\Api\V1\ValidationLogController;
use App\Http\Controllers\Api\V1\ValidationLogCommentController;
use App\Http\Controllers\Api\V1\ProfessionalRegistrationController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Controllers\Api\V1\CustodianModelConfigController;
use App\Http\Controllers\Api\V1\ProjectDetailController;
use App\Http\Controllers\Api\V1\ProjectRoleController;
use App\Http\Controllers\Api\V1\CustodianHasProjectUserController;
use App\Http\Controllers\Api\V1\ProjectHasUserController;
use App\Http\Controllers\Api\V1\ProjectHasOrganisationController;
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\VendorWebhookReceiverController;
use App\Http\Controllers\Api\V1\SubsidiaryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['check.custodian.access', 'verify.signed.payload'])->post('v1/query', [QueryController::class, 'query']);

// --- AUTH ---
Route::middleware('api')->get('auth/me', [AuthController::class, 'me']);
Route::middleware('api')->post('auth/register', [AuthController::class, 'registerKeycloakUser']);
Route::middleware('api')->post('auth/claimUser', [AuthController::class, 'claimUser']);

// --- USERS ---
Route::middleware(['auth:api'])
    ->prefix('v1/users')
    ->group(function () {

        Route::get('/', [UserController::class, 'index']);
        Route::get('/test', [UserController::class, 'fakeEndpointForTesting']);
        Route::get('/identifier', [UserController::class, 'showByUniqueIdentifier']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/{id}/history', [AuditLogController::class, 'showUserHistory']);
        Route::get('/{id}/projects', [UserController::class, 'userProjects']);

        // create
        Route::post('/', [UserController::class, 'store']);
        Route::post('/invite', [UserController::class, 'invite']);
        Route::post('/permissions', [PermissionController::class, 'assignUserPermissionsToFrom']);
        Route::post('/search_affiliations', [UserController::class, 'searchUsersByNameAndProfessionalEmail']);


        //update
        Route::put('/{id}', [UserController::class, 'update']);
        Route::patch('/{id}', [UserController::class, 'edit']);
        Route::delete('/{id}', [UserController::class, 'destroy']);


        // Notifications
        Route::get('/{id}/notifications', [NotificationController::class, 'getUserNotifications']);
        Route::get('/{id}/notifications/count', [NotificationController::class, 'getNotificationCounts']);
        Route::patch('/{id}/notifications/read', [NotificationController::class, 'markUserNotificationsAsRead']);
        Route::patch('/{id}/notifications/{notificationId}/read', [NotificationController::class, 'markUserNotificationAsRead']);
        Route::patch('/{id}/notifications/{notificationId}/unread', [NotificationController::class, 'markUserNotificationAsUnread']);
    });

// --- ACTION LOGS ---
Route::middleware('auth:api')
    ->prefix('v1')
    ->controller(ActionLogController::class)
    ->group(function () {
        Route::get('{entity}/{id}/action_log', 'getEntityActionLog');
        Route::put('action_log/{id}', 'update');
    });


// --- VALIDATION LOGS ---
Route::middleware('auth:api')
    ->prefix('v1')
    ->group(function () {

        // /validation_logs
        Route::prefix('validation_logs')
            ->controller(ValidationLogController::class)
            ->group(function () {
                Route::get('{id}', 'index');
                Route::get('{id}/comments', 'comments');
                Route::put('{id}', 'update');
            });

        // /custodians/.../validation_logs
        Route::prefix('custodians')
            ->controller(ValidationLogController::class)
            ->group(function () {
                Route::get('{custodianId}/projects/{projectId}/registries/{registryId}/validation_logs', 'getCustodianProjectUserValidationLogs');
                Route::get('{custodianId}/organisations/{organisationId}/validation_logs', 'getCustodianOrganisationValidationLogs');
                Route::put('{custodianId}/validation_logs', 'updateCustodianValidationLogs');
            });
    });

// --- VALIDATION LOG COMMENTS ---
Route::middleware('auth:api')
    ->prefix('v1/validation_log_comments')
    ->controller(ValidationLogCommentController::class)
    ->group(function () {
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

// --- VALIDATION CHECKS ---
Route::middleware('auth:api')
    ->prefix('v1')
    ->controller(ValidationCheckController::class)
    ->group(function () {

        Route::prefix('validation_checks')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::post('/', 'store');
                Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

        Route::prefix('custodians/{custodianId}/validation_checks')
            ->group(function () {
                Route::get('/', 'getCustodianValidationChecks');
                Route::post('/', 'createCustodianValidationChecks');
            });
    });


// --- TRAINING ---
Route::middleware('auth:api')
    ->prefix('v1/training')
    ->controller(TrainingController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::get('/registry/{registryId}', 'indexByRegistryId');

        Route::post('/', 'store');
        Route::post('/{trainingId}/link_file/{fileId}', 'linkTrainingFile');

        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

// --- CUSTODIANS ---
Route::middleware(['auth:api'])
    ->prefix('v1/custodians')
    ->controller(CustodianController::class)
    ->group(function () {
        // Read
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::get('/identifier/{id}', 'showByUniqueIdentifier');
        Route::get('/{id}/projects', 'getProjects');
        Route::get('/{id}/users/{userId}/projects', 'getUserProjects');
        Route::get('/{id}/organisations', 'getProjectsOrganisations');
        Route::get('/{id}/custodian_users', 'getCustodianUsers');
        Route::get('/{id}/projects_users', 'getProjectsUsers');
        Route::get('/{id}/rules', 'getRules');
        Route::get('/{id}/organisations/{organisationId}/users', 'getOrganisationUsers');
        Route::get('/{id}/organisations/{organisationId}/projects/{projectId}/users/{userId}/statuses', 'getStatusesUsers');

        // Write
        Route::post('/', 'store');
        Route::post('/push', 'push');
        Route::post('/{id}/invite', 'invite');
        Route::post('/{id}/projects', 'addProject');

        // Update
        Route::put('/{id}', 'update');
        Route::patch('/{id}/rules', 'updateCustodianRules');

        // Delete
        Route::delete('/{id}', 'destroy');
    });


// --- CUSTODIAN USERS ---
Route::middleware('auth:api')
    ->prefix('v1/custodian_users')
    ->controller(CustodianUserController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
        Route::post('invite/{id}', 'invite');
    });

// --- DEPARTMENTS ---
Route::middleware('auth:api')
    ->prefix('v1/departments')
    ->controller(DepartmentController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::patch('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

// --- ENDORSEMENTS ---
Route::middleware('auth:api')
    ->prefix('v1/endorsements')
    ->controller(EndorsementController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
    });

// --- PROJECTS ---
Route::middleware('auth:api')
    ->prefix('v1/projects')
    ->controller(ProjectController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');

        // Project user management
        Route::get('user/{registryId}/validated', 'getValidatedProjects');
        Route::get('{id}/users', 'getProjectUsers');
        Route::get('{id}/all_users', 'getAllUsersFlagProject');
        Route::put('{id}/all_users', 'updateAllProjectUsers');
        Route::post('{id}/users', 'addProjectUser');
        Route::put('{projectId}/users/{registryId}', 'updateProjectUser');
        Route::delete('{projectId}/users/registry/{registryId}', 'deleteUserFromProject');
        Route::delete('{projectId}/organisations/{organisationId}', 'deleteOrganisationFromProject');
        Route::put('{projectId}/users/{registryId}/primary_contact', 'makePrimaryContact');

        Route::get('{projectId}/organisations/{organisationId}', 'getProjectByIdAndOrganisationId');
        Route::get('{projectId}/organisations/{organisationId}/users', 'getProjectUsersByOrganisationId');
    });

// --- REGISTRIES ---
Route::middleware('auth:api')
    ->prefix('v1/registries')
    ->controller(RegistryController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

// --- EXPERIENCES ---
Route::middleware('auth:api')
    ->prefix('v1/experiences')
    ->controller(ExperienceController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

// --- IDENTITIES ---
Route::middleware('auth:api')
    ->prefix('v1/identities')
    ->controller(IdentityController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

// --- ORGANISATIONS ---
Route::middleware('auth:api')
    ->prefix('v1/organisations')
    ->group(function () {
        Route::controller(OrganisationController::class)->group(function () {
            // Read
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::get('/{id}/idvt', 'idvt');
            Route::get('/{id}/counts/certifications', 'countCertifications');
            Route::get('/{id}/counts/users', 'countUsers');
            Route::get('/{id}/counts/projects/present', 'countPresentProjects');
            Route::get('/{id}/counts/projects/past', 'countPastProjects');
            Route::get('/{id}/projects/present', 'presentProjects');
            Route::get('/{id}/projects/past', 'pastProjects');
            Route::get('/{id}/projects/future', 'futureProjects');
            Route::get('/{id}/projects', 'getProjects');
            Route::get('/{id}/users', 'getUsers');
            Route::get('/{id}/delegates', 'getDelegates');
            Route::get('/{id}/registries', 'getRegistries');
            Route::get('/ror/{ror}', 'validateRor');

            // Create
            Route::post('/', 'store');
            //Route::post('/unclaimed', 'inviteOrganisationSimple');
            Route::post('/unclaimed', 'storeUnclaimed');
            Route::post('/{id}/invite', 'invite');
            Route::post('/{id}/invite_user', 'inviteUser');

            // Update
            Route::put('/{id}', 'update');

            // Delete
            Route::delete('/{id}', 'destroy');
        });

        Route::controller(PermissionController::class)->group(function () {
            Route::post('/permissions', 'assignOrganisationPermissionsToFrom');
        });
    });

// --- SUBSIDIARIES ---
Route::middleware('auth:api')
    ->prefix('v1/subsidiaries')
    ->group(function () {
        Route::controller(SubsidiaryController::class)->group(function () {
            Route::post('organisations/{orgId}', 'store');
            Route::put('{id}/organisations/{orgId}', 'update');
            Route::delete('{id}/organisations/{orgId}', 'destroy');
        });
    });

// --- ACCREDITATIONS ---

// --- ACCREDITATIONS ---
Route::middleware('auth:api')
    ->prefix('v1/accreditations')
    ->controller(AccreditationController::class)
    ->group(function () {
        Route::get('{registryId}', 'indexByRegistryId');
        Route::post('{registryId}', 'storeByRegistryId');
        Route::put('{id}/{registryId}', 'updateByRegistryId');
        Route::patch('{id}/{registryId}', 'editByRegistryId');
        Route::delete('{id}/{registryId}', 'destroyByRegistryId');
    });

// --- AFFILIATIONS ---
Route::middleware('auth:api')
    ->prefix('v1/affiliations')
    ->controller(AffiliationController::class)
    ->group(function () {
        Route::get('workflowStates', 'getWorkflowStates');
        Route::get('workflowTransitions', 'getWorkflowTransitions');
        Route::get('{registryId}', 'indexByRegistryId');
        Route::get('{registryId}/organisation/{organisationId}', 'getOrganisationAffiliation');
        Route::post('{registryId}', 'storeByRegistryId');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
        Route::put('{registryId}/affiliation/{id}', 'updateRegistryAffiliation');
    });

// --- PROFESSIONAL REGISTRATIONS ---
Route::middleware('auth:api')
    ->prefix('v1/professional_registrations')
    ->controller(ProfessionalRegistrationController::class)
    ->group(function () {
        Route::get('registry/{registryId}', 'indexByRegistryId');
        Route::post('registry/{registryId}', 'storeByRegistryId');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

// --- EDUCATIONS ---
Route::middleware('auth:api')
    ->prefix('v1/educations')
    ->controller(EducationController::class)
    ->group(function () {
        Route::get('{registryId}', 'indexByRegistryId');
        Route::get('{id}/{registryId}', 'showByRegistryId');
        Route::post('{registryId}', 'storeByRegistryId');
        Route::put('{id}/{registryId}', 'updateByRegistryId');
        Route::patch('{id}/{registryId}', 'editByRegistryId');
        Route::delete('{id}/{registryId}', 'destroyByRegistryId');
    });

// --- SECTORS ---
Route::middleware('auth:api')
    ->prefix('v1/sectors')
    ->controller(SectorController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

// --- RESOLUTIONS ---
Route::middleware('auth:api')
    ->prefix('v1/resolutions')
    ->controller(ResolutionController::class)
    ->group(function () {
        Route::get('{registryId}', 'indexByRegistryId');
        Route::post('{registryId}', 'storeByRegistryId');
    });

// --- HISTORIES ---
Route::middleware('auth:api')
    ->prefix('v1/histories')
    ->controller(HistoryController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
    });

// --- INFRINGEMENTS ---
Route::middleware('auth:api')
    ->prefix('v1/infringements')
    ->controller(InfringementController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
    });

// --- PERMISSIONS ---
Route::middleware('auth:api')->get('v1/permissions', [PermissionController::class, 'index']);

// --- EMAIL TEMPLATES ---
Route::middleware('auth:api')->get('v1/email_templates', [EmailTemplateController::class, 'index']);

// --- TRIGGER EMAIL ---
Route::middleware('auth:api')->post('v1/trigger_email', [TriggerEmailController::class, 'spawnEmail']);

// --- FILE UPLOADS ---
Route::middleware('auth:api')
    ->prefix('v1/files')
    ->controller(FileUploadController::class)
    ->group(function () {
        Route::post('/', 'store');
        Route::get('{id}', 'show');
        Route::get('{id}/download', 'download');
    });


// --- REQUEST ACCESS ---
Route::middleware(['check.custodian.access', 'verify.signed.payload'])
    ->post('v1/request_access', [RegistryReadRequestController::class, 'request']);
Route::middleware('auth:api')->patch('v1/request_access/{id}', [RegistryReadRequestController::class, 'acceptOrReject']);

// --- WEBHOOKS ---
Route::middleware('auth:api')
    ->prefix('v1/webhooks')
    ->controller(WebhookController::class)
    ->group(function () {
        Route::get('receivers', 'getAllReceivers');
        Route::get('receivers/{custodianId}', 'getReceiversByCustodian');
        Route::post('receivers', 'createReceiver');
        Route::put('receivers/{custodianId}', 'updateReceiver');
        Route::delete('receivers/{custodianId}', 'deleteReceiver');
        Route::get('event-triggers', 'getAllEventTriggers');
    });

// --- CUSTODIAN CONFIG ---
Route::middleware('auth:api')
    ->prefix('v1/custodian_config')
    ->controller(CustodianModelConfigController::class)
    ->group(function () {
        Route::post('/', 'store');
        Route::get('{id}', 'getByCustodianID');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
        Route::get('{id}/entity_models', 'getEntityModels');
        Route::put('{id}/entity_models', 'updateEntityModels');
    });

// --- PROJECT DETAILS ---
Route::middleware('auth:api')
    ->prefix('v1/project_details')
    ->controller(ProjectDetailController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
        Route::post('query_gateway_dur', 'queryGatewayDurByProjectID');
    });

// --- PROJECT ROLES ---
Route::middleware('auth:api')
    ->prefix('v1/project_roles')
    ->controller(ProjectRoleController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('{id}', 'update');
    });

// --- PROJECT USER  ---
Route::middleware('auth:api')
    ->prefix('v1/project_users')
    ->controller(ProjectHasUserController::class)
    ->group(function () {
        Route::get('/{id}', 'show');
        Route::delete('/{id}', 'delete');
    });

// --- PROJECT ORGANISATION  ---
Route::middleware('auth:api')
    ->prefix('v1/project_organisations')
    ->controller(ProjectHasOrganisationController::class)
    ->group(function () {
        Route::get('/{id}', 'show');
    });

// --- CUSTODIAN PROJECT USERS VALIDATIONS ---
Route::middleware('auth:api')
    ->prefix('v1/custodian_approvals/{custodianId}')
    ->controller(CustodianHasProjectUserController::class)
    ->group(function () {
        Route::get('/projectUsers', 'index');
        Route::get('/projectUsers/{projectUserId}', 'show');
        Route::put('/projectUsers/{projectUserId}', 'update');
    });

Route::middleware('auth:api')
    ->prefix('v1/custodian_approvals/projectUsers')
    ->controller(CustodianHasProjectUserController::class)
    ->group(function () {
        Route::get('/workflowStates', 'getWorkflowStates');
        Route::get('/workflowTransitions', 'getWorkflowTransitions');
    });


// --- ORGANISATION CUSTODIAN VALIDATIONS ---
Route::middleware('auth:api')
    ->prefix('v1/custodian_approvals/{custodianId}')
    ->controller(CustodianHasProjectOrganisationController::class)
    ->group(function () {
        Route::get('/projectOrganisations', 'index');
        Route::get('/projectOrganisations/{projectOrganisationId}', 'show');
        Route::put('/projectOrganisations/{projectOrganisationId}', 'update');
    });

Route::middleware('auth:api')
    ->prefix('v1/custodian_approvals/projectOrganisations')
    ->controller(CustodianHasProjectOrganisationController::class)
    ->group(function () {
        Route::get('/workflowStates', 'getWorkflowStates');
        Route::get('/workflowTransitions', 'getWorkflowTransitions');
    });



// --- SYSTEM CONFIG ---
Route::middleware('auth:api')
    ->prefix('v1/system_config')
    ->controller(SystemConfigController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{name}', 'getByName');
    });

// --- RULES ---
Route::middleware('auth:api')->get('v1/rules', [RulesEngineManagementController::class, 'getRules']);

Route::post('v1/webhooks/{provider}', [VendorWebhookReceiverController::class, 'receive']);

// ONS CSV RESEARCHER FEED
Route::post('v1/ons_researcher_feed', [ONSSubmissionController::class, 'receiveCSV']);

// stop all all other routes
Route::any('{path}', function () {
    $response = [
        'message' => 'Resource not found',
    ];

    return response()->json($response)
        ->setStatusCode(404);
});
