<?php

use App\Http\Controllers\Api\V1\AccreditationController;
use App\Http\Controllers\Api\V1\AffiliationController;
use App\Http\Controllers\Api\V1\ApprovalController;
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
use App\Http\Controllers\Api\V1\OrganisationDelegatesController;
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
use App\Http\Controllers\Api\V1\ValidationLogController;
use App\Http\Controllers\Api\V1\ValidationLogCommentController;
use App\Http\Controllers\Api\V1\ProfessionalRegistrationController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Controllers\Api\V1\CustodianModelConfigController;
use App\Http\Controllers\Api\V1\ProjectDetailController;
use App\Http\Controllers\Api\V1\ProjectRoleController;
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

Route::middleware('api')->get('auth/me', [AuthController::class, 'me']);
Route::middleware('api')->post('auth/register', [AuthController::class, 'registerKeycloakUser']);

Route::middleware('auth:api')->get('v1/users', [UserController::class, 'index']);
Route::middleware('auth:api')->get('v1/users/test', [UserController::class, 'fakeEndpointForTesting']);
Route::middleware('auth:api')->get('v1/users/{id}', [UserController::class, 'show']);
Route::middleware('auth:api')->get('v1/users/identifier/{id}', [UserController::class, 'showByUniqueIdentifier']);
Route::middleware('auth:api')->post('v1/users', [UserController::class, 'store']);
Route::middleware('auth:api')->put('v1/users/{id}', [UserController::class, 'update']);
Route::middleware('auth:api')->patch('v1/users/{id}', [UserController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/users/{id}', [UserController::class, 'destroy']);
Route::middleware('auth:api')->post('v1/users/invite', [UserController::class, 'invite']);
Route::middleware('auth:api')->post('v1/users/permissions', [PermissionController::class, 'assignUserPermissionsToFrom']);
Route::middleware('auth:api')->post('v1/users/change-password/{userId}', [AuthController::class, 'changePassword']);
Route::middleware(['check.custodian.access', 'verify.signed.payload'])->post('v1/users/validate', [UserController::class, 'validateUserRequest']);
Route::middleware('auth:api')->post('v1/users/search_affiliations', [UserController::class, 'searchUsersByNameAndProfessionalEmail']);
Route::middleware('auth:api')->get('v1/users/{id}/projects', [UserController::class, 'userProjects']);

Route::middleware('auth:api')->get('v1/users/{id}/notifications', [NotificationController::class, 'getUserNotifications']);
Route::middleware('auth:api')->get('v1/users/{id}/notifications/count', [NotificationController::class, 'getNotificationCounts']);
Route::middleware('auth:api')->patch('v1/users/{id}/notifications/read', [NotificationController::class, 'markUserNotificationsAsRead']);
Route::middleware('auth:api')->patch('v1/users/{userId}/notifications/{notificationId}/read', [NotificationController::class, 'markUserNotificationAsRead']);
Route::middleware('auth:api')->patch('v1/users/{userId}/notifications/{notificationId}/unread', [NotificationController::class, 'markUserNotificationAsUnread']);

Route::middleware('auth:api')->get('v1/{entity}/{id}/action_log', [ActionLogController::class, 'getEntityActionLog']);
Route::middleware('auth:api')->put('v1/action_log/{id}', [ActionLogController::class, 'update']);

Route::middleware('auth:api')->get(
    'v1/custodians/{custodianId}/projects/{projectId}/registries/{registryId}/validation_logs',
    [ValidationLogController::class, 'getCustodianProjectUserValidationLogs']
);
Route::middleware('auth:api')->put(
    'v1/custodians/{custodianId}/validation_logs',
    [ValidationLogController::class, 'updateCustodianValidationLogs']
);


Route::middleware('auth:api')->get('v1/validation_logs/{id}', [ValidationLogController::class, 'index']);
Route::middleware('auth:api')->get('v1/validation_logs/{id}/commments', [ValidationLogController::class, 'comments']);
Route::middleware('auth:api')->put('v1/validation_logs/{id}', [ValidationLogController::class, 'update']);


Route::middleware('auth:api')->get('v1/validation_log_comments/{id}', [ValidationLogCommentController::class, 'show']);
Route::middleware('auth:api')->post('v1/validation_log_comments', [ValidationLogCommentController::class, 'store']);
Route::middleware('auth:api')->put('v1/validation_log_comments/{id}', [ValidationLogCommentController::class, 'update']);
Route::middleware('auth:api')->delete('v1/validation_log_comments/{id}', [ValidationLogCommentController::class, 'destroy']);



Route::middleware('auth:api')->get('v1/training', [TrainingController::class, 'index']);
Route::middleware('auth:api')->get('v1/training/registry/{registryId}', [TrainingController::class, 'indexByRegistryId']);
Route::middleware('auth:api')->get('v1/training/{id}', [TrainingController::class, 'show']);
Route::middleware('auth:api')->post('v1/training', [TrainingController::class, 'store']);
Route::middleware('auth:api')->put('v1/training/{id}', [TrainingController::class, 'update']);
Route::middleware('auth:api')->delete('v1/training/{id}', [TrainingController::class, 'destroy']);
Route::middleware('auth:api')->post('v1/training/{trainingId}/link_file/{fileId}', [TrainingController::class, 'linkTrainingFile']);

Route::middleware('auth:api')->get('v1/custodians', [CustodianController::class, 'index']);
Route::middleware('auth:api')->get('v1/custodians/{id}', [CustodianController::class, 'show']);
Route::middleware('auth:api')->get('v1/custodians/identifier/{id}', [CustodianController::class, 'showByUniqueIdentifier']);
Route::middleware('auth:api')->post('v1/custodians/{id}/invite', [CustodianController::class, 'invite']);
Route::middleware('auth:api')->get('v1/custodians/{id}/projects', [CustodianController::class, 'getProjects']);
Route::middleware('auth:api')->get('v1/custodians/{id}/users/{userId}/projects', [CustodianController::class, 'getUserProjects']);
Route::middleware('auth:api')->get('v1/custodians/{id}/organisations', [CustodianController::class, 'getOrganisations']);

Route::middleware('auth:api')->get('v1/custodians/{id}/projects_users', [CustodianController::class, 'getProjectsUsers']);
Route::middleware('auth:api')->post('v1/custodians', [CustodianController::class, 'store']);
Route::middleware('auth:api')->put('v1/custodians/{id}', [CustodianController::class, 'update']);
Route::middleware('auth:api')->patch('v1/custodians/{id}', [CustodianController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/custodians/{id}', [CustodianController::class, 'destroy']);
Route::middleware(['auth:api', 'check.custodian.access'])->post('v1/custodians/push', [CustodianController::class, 'push']);
Route::middleware('auth:api')->get('v1/custodians/{id}/rules', [CustodianController::class, 'getRules']);
Route::middleware('auth:api')->patch('v1/custodians/{id}/rules', [CustodianController::class, 'updateCustodianRules']);
Route::middleware('auth:api')->get('v1/custodians/{id}/users', [CustodianController::class, 'usersWithCustodianApprovals']);

Route::middleware('auth:api')->get('v1/custodian_users', [CustodianUserController::class, 'index']);
Route::middleware('auth:api')->get('v1/custodian_users/{id}', [CustodianUserController::class, 'show']);
Route::middleware('auth:api')->post('v1/custodian_users', [CustodianUserController::class, 'store']);
Route::middleware('auth:api')->put('v1/custodian_users/{id}', [CustodianUserController::class, 'update']);
Route::middleware('auth:api')->patch('v1/custodian_users/{id}', [CustodianUserController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/custodian_users/{id}', [CustodianUserController::class, 'destroy']);
Route::middleware('auth:api')->post('v1/custodian_users/invite/{id}', [CustodianUserController::class, 'invite']);

Route::middleware('auth:api')->get('v1/departments', [DepartmentController::class, 'index']);
Route::middleware('auth:api')->get('v1/departments/{id}', [DepartmentController::class, 'show']);
Route::middleware('auth:api')->post('v1/departments', [DepartmentController::class, 'store']);
Route::middleware('auth:api')->patch('v1/departments/{id}', [DepartmentController::class, 'update']);
Route::middleware('auth:api')->delete('v1/departments/{id}', [DepartmentController::class, 'destroy']);

Route::middleware('auth:api')->get('v1/endorsements', [EndorsementController::class, 'index']);
Route::middleware('auth:api')->get('v1/endorsements/{id}', [EndorsementController::class, 'show']);
Route::middleware('auth:api')->post('v1/endorsements', [EndorsementController::class, 'store']);

Route::middleware('auth:api')->get('v1/projects', [ProjectController::class, 'index']);
Route::middleware('auth:api')->get('v1/projects/{id}', [ProjectController::class, 'show']);

Route::middleware('auth:api')->get('v1/projects/user/{registryId}/approved', [ProjectController::class, 'getApprovedProjects']);
Route::middleware('auth:api')->get('v1/projects/{id}/users', [ProjectController::class, 'getProjectUsers']);
Route::middleware('auth:api')->get('v1/projects/{id}/all_users', [ProjectController::class, 'getAllUsersFlagProject']);
Route::middleware('auth:api')->put('v1/projects/{id}/all_users', [ProjectController::class, 'updateAllProjectUsers']);
Route::middleware('auth:api')->post('v1/projects/{id}/users', [ProjectController::class, 'addProjectUser']);
Route::middleware('auth:api')->put('v1/projects/{projectId}/users/{registryId}', [ProjectController::class, 'updateProjectUser']);
Route::middleware('auth:api')->delete('v1/projects/{projectId}/users/{registryId}', [ProjectController::class, 'deleteUserFromProject']);


Route::middleware('auth:api')->put('v1/projects/{projectId}/users/{registryId}/primary_contact', [ProjectController::class, 'makePrimaryContact']);
Route::middleware('auth:api')->post('v1/projects', [ProjectController::class, 'store']);
Route::middleware('auth:api')->put('v1/projects/{id}', [ProjectController::class, 'update']);
Route::middleware('auth:api')->patch('v1/projects/{id}', [ProjectController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/projects/{id}', [ProjectController::class, 'destroy']);

Route::middleware('auth:api')->get('v1/registries', [RegistryController::class, 'index']);
Route::middleware('auth:api')->get('v1/registries/{id}', [RegistryController::class, 'show']);
Route::middleware('auth:api')->post('v1/registries', [RegistryController::class, 'store']);
Route::middleware('auth:api')->put('v1/registries/{id}', [RegistryController::class, 'update']);
Route::middleware('auth:api')->patch('v1/registries/{id}', [RegistryController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/registries/{id}', [RegistryController::class, 'destroy']);

Route::middleware('auth:api')->get('v1/experiences', [ExperienceController::class, 'index']);
Route::middleware('auth:api')->get('v1/experiences/{id}', [ExperienceController::class, 'show']);
Route::middleware('auth:api')->post('v1/experiences', [ExperienceController::class, 'store']);
Route::middleware('auth:api')->put('v1/experiences/{id}', [ExperienceController::class, 'update']);
Route::middleware('auth:api')->patch('v1/experiences/{id}', [ExperienceController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/experiences/{id}', [ExperienceController::class, 'destroy']);

Route::middleware('auth:api')->get('v1/identities', [IdentityController::class, 'index']);
Route::middleware('auth:api')->get('v1/identities/{id}', [IdentityController::class, 'show']);
Route::middleware('auth:api')->post('v1/identities', [IdentityController::class, 'store']);
Route::middleware('auth:api')->put('v1/identities/{id}', [IdentityController::class, 'update']);
Route::middleware('auth:api')->patch('v1/identities/{id}', [IdentityController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/identities/{id}', [IdentityController::class, 'destroy']);

Route::middleware('auth:api')->get('v1/organisations', [OrganisationController::class, 'index']);
Route::middleware('auth:api')->get('v1/organisations/{id}', [OrganisationController::class, 'show']);
Route::middleware('auth:api')->get('v1/organisations/{id}/idvt', [OrganisationController::class, 'idvt']);
Route::middleware('auth:api')->get('v1/organisations/{id}/counts/certifications', [OrganisationController::class, 'countCertifications']);
Route::middleware('auth:api')->get('v1/organisations/{id}/counts/users', [OrganisationController::class, 'countUsers']);
Route::middleware('auth:api')->get('v1/organisations/{id}/counts/projects/present', [OrganisationController::class, 'countPresentProjects']);
Route::middleware('auth:api')->get('v1/organisations/{id}/counts/projects/past', [OrganisationController::class, 'countPastProjects']);
Route::middleware('auth:api')->post('v1/organisations', [OrganisationController::class, 'store']);
Route::middleware('auth:api')->post('v1/organisations/unclaimed', [OrganisationController::class, 'storeUnclaimed']);
Route::middleware('auth:api')->put('v1/organisations/{id}', [OrganisationController::class, 'update']);
Route::middleware('auth:api')->patch('v1/organisations/{id}', [OrganisationController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/organisations/{id}', [OrganisationController::class, 'destroy']);
Route::middleware('auth:api')->post('v1/organisations/{id}/invite', [OrganisationController::class, 'invite']);
Route::middleware('auth:api')->post('v1/organisations/{id}/invite_user', [OrganisationController::class, 'inviteUser']);
Route::middleware('auth:api')->post('v1/organisations/permissions', [PermissionController::class, 'assignOrganisationPermissionsToFrom']);
Route::middleware('auth:api')->get('v1/organisations/{id}/projects', [OrganisationController::class, 'getProjects']);
Route::middleware('auth:api')->get('v1/organisations/{id}/users', [OrganisationController::class, 'getUsers']);
Route::middleware('auth:api')->get('v1/organisations/{id}/delegates', [OrganisationController::class, 'getDelegates']);
Route::middleware('auth:api')->get('v1/organisations/{id}/registries', [OrganisationController::class, 'getRegistries']);

// Route::middleware('api')->get('v1/organisation_delegates', [OrganisationDelegatesController::class, 'index']);
// Route::middleware('api')->get('v1/organisation_delegates/{id}', [OrganisationDelegatesController::class, 'show']);
// Route::middleware('api')->post('v1/organisation_delegates', [OrganisationDelegatesController::class, 'store']);
// Route::middleware('api')->put('v1/organisation_delegates/{id}', [OrganisationDelegatesController::class, 'update']);
// Route::middleware('api')->patch('v1/organisation_delegates/{id}', [OrganisationDelegatesController::class, 'edit']);
// Route::middleware('api')->delete('v1/organisation_delegates/{id}', [OrganisationDelegatesController::class, 'destroy']);


Route::middleware('auth:api')->get('v1/organisations/{id}/projects/present', [OrganisationController::class, 'presentProjects']);
Route::middleware('auth:api')->get('v1/organisations/{id}/projects/past', [OrganisationController::class, 'pastProjects']);
Route::middleware('auth:api')->get('v1/organisations/{id}/projects/future', [OrganisationController::class, 'futureProjects']);

Route::middleware('auth:api')->get('v1/organisations/ror/{ror}', [OrganisationController::class, 'validateRor']);

Route::middleware('auth:api')->get('v1/accreditations/{registryId}', [AccreditationController::class, 'indexByRegistryId']);
Route::middleware('auth:api')->post('v1/accreditations/{registryId}', [AccreditationController::class, 'storeByRegistryId']);
Route::middleware('auth:api')->put('v1/accreditations/{id}/{registryId}', [AccreditationController::class, 'updateByRegistryId']);
Route::middleware('auth:api')->patch('v1/accreditations/{id}/{registryId}', [AccreditationController::class, 'editByRegistryId']);
Route::middleware('auth:api')->delete('v1/accreditations/{id}/{registryId}', [AccreditationController::class, 'destroyByRegistryId']);

Route::middleware('auth:api')->get('v1/affiliations/{registryId}', [AffiliationController::class, 'indexByRegistryId']);
Route::middleware('auth:api')->post('v1/affiliations/{registryId}', [AffiliationController::class, 'storeByRegistryId']);
Route::middleware('auth:api')->put('v1/affiliations/{id}', [AffiliationController::class, 'update']);
Route::middleware('auth:api')->patch('v1/affiliations/{id}', [AffiliationController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/affiliations/{id}', [AffiliationController::class, 'destroy']);
Route::middleware('auth:api')->put('v1/affiliations/{registryId}/affiliation/{id}', [AffiliationController::class, 'updateRegistryAffiliation']);


Route::middleware('auth:api')->get('v1/professional_registrations/registry/{registryId}', [ProfessionalRegistrationController::class, 'indexByRegistryId']);
Route::middleware('auth:api')->post('v1/professional_registrations/registry/{registryId}', [ProfessionalRegistrationController::class, 'storeByRegistryId']);
Route::middleware('auth:api')->put('v1/professional_registrations/{id}', [ProfessionalRegistrationController::class, 'update']);
Route::middleware('auth:api')->patch('v1/professional_registrations/{id}', [ProfessionalRegistrationController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/professional_registrations/{id}', [ProfessionalRegistrationController::class, 'destroy']);

Route::middleware('auth:api')->get('v1/educations/{registryId}', [EducationController::class, 'indexByRegistryId']);
Route::middleware('auth:api')->get('v1/educations/{id}/{registryId}', [EducationController::class, 'showByRegistryId']);
Route::middleware('auth:api')->post('v1/educations/{registryId}', [EducationController::class, 'storeByRegistryId']);
Route::middleware('auth:api')->put('v1/educations/{id}/{registryId}', [EducationController::class, 'updateByRegistryId']);
Route::middleware('auth:api')->patch('v1/educations/{id}/{registryId}', [EducationController::class, 'editByRegistryId']);
Route::middleware('auth:api')->delete('v1/educations/{id}/{registryId}', [EducationController::class, 'destroyByRegistryId']);

Route::middleware('auth:api')->get('v1/sectors', [SectorController::class, 'index']);
Route::middleware('auth:api')->get('v1/sectors/{id}', [SectorController::class, 'show']);
Route::middleware('auth:api')->post('v1/sectors', [SectorController::class, 'store']);
Route::middleware('auth:api')->put('v1/sectors/{id}', [SectorController::class, 'update']);
Route::middleware('auth:api')->patch('v1/sectors/{id}', [SectorController::class, 'edit']);
Route::middleware('auth:api')->delete('v1/sectors/{id}', [SectorController::class, 'destroy']);

Route::middleware('auth:api')->get('v1/resolutions/{registryId}', [ResolutionController::class, 'indexByRegistryId']);
Route::middleware('auth:api')->post('v1/resolutions/{registryId}', [ResolutionController::class, 'storeByRegistryId']);

Route::middleware('auth:api')->get('v1/histories', [HistoryController::class, 'index']);
Route::middleware('auth:api')->get('v1/histories/{id}', [HistoryController::class, 'show']);
Route::middleware('auth:api')->post('v1/histories', [HistoryController::class, 'store']);

Route::middleware('auth:api')->get('v1/infringements', [InfringementController::class, 'index']);
Route::middleware('auth:api')->get('v1/infringements/{id}', [InfringementController::class, 'show']);
Route::middleware('auth:api')->post('v1/infringements', [InfringementController::class, 'store']);

Route::middleware('auth:api')->get('v1/permissions', [PermissionController::class, 'index']);

Route::middleware('auth:api')->get('v1/email_templates', [EmailTemplateController::class, 'index']);

Route::middleware('auth:api')->post('v1/trigger_email', [TriggerEmailController::class, 'spawnEmail']);

Route::middleware('auth:api')->post('v1/files', [FileUploadController::class, 'store']);
Route::middleware('auth:api')->get('v1/files/{id}', [FileUploadController::class, 'show']);
Route::middleware('auth:api')->get('v1/files/{id}/download', [FileUploadController::class, 'download']);


Route::middleware('auth:api')->post('v1/approvals/{entity_type}', [ApprovalController::class, 'store']);
Route::middleware('auth:api')->get('v1/approvals/{entity_type}/{id}/custodian/{custodian_id}', [ApprovalController::class, 'getEntityHasCustodianApproval']);
Route::middleware('auth:api')->delete('v1/approvals/{entity_type}/{id}/custodian/{custodian_id}', [ApprovalController::class, 'delete']);

Route::middleware('auth:api')->post('v1/request_access', [RegistryReadRequestController::class, 'request']);
Route::middleware('auth:api')->patch('v1/request_access/{id}', [RegistryReadRequestController::class, 'acceptOrReject']);

Route::middleware('auth:api')->get('v1/webhooks/receivers', [WebhookController::class, 'getAllReceivers']);
Route::middleware('auth:api')->get('v1/webhooks/receivers/{custodianId}', [WebhookController::class, 'getReceiversByCustodian']);
Route::middleware('auth:api')->post('v1/webhooks/receivers', [WebhookController::class, 'createReceiver']);
Route::middleware('auth:api')->put('v1/webhooks/receivers/{custodianId}', [WebhookController::class, 'updateReceiver']);
Route::middleware('auth:api')->delete('v1/webhooks/receivers/{custodianId}', [WebhookController::class, 'deleteReceiver']);
Route::middleware('auth:api')->get('v1/webhooks/event-triggers', [WebhookController::class, 'getAllEventTriggers']);

Route::middleware('auth:api')->put('v1/custodian_config/update-active/{id}', [CustodianModelConfigController::class, 'updateCustodianModelConfigsActive']);
Route::middleware('auth:api')->post('v1/custodian_config', [CustodianModelConfigController::class, 'store']);
Route::middleware('auth:api')->get('v1/custodian_config/{id}', [CustodianModelConfigController::class, 'getByCustodianID']);
Route::middleware('auth:api')->put('v1/custodian_config/{id}', [CustodianModelConfigController::class, 'update']);
Route::middleware('auth:api')->delete('v1/custodian_config/{id}', [CustodianModelConfigController::class, 'destroy']);
Route::middleware('auth:api')->get('v1/custodian_config/{id}/entity_models', [CustodianModelConfigController::class, 'getEntityModels']);

Route::middleware('auth:api')->get('v1/project_details', [ProjectDetailController::class, 'index']);
Route::middleware('auth:api')->get('v1/project_details/{id}', [ProjectDetailController::class, 'show']);
Route::middleware('auth:api')->post('v1/project_details', [ProjectDetailController::class, 'store']);
Route::middleware('auth:api')->put('v1/project_details/{id}', [ProjectDetailController::class, 'update']);
Route::middleware('auth:api')->delete('v1/project_details/{id}', [ProjectDetailController::class, 'destroy']);
Route::middleware('auth:api')->post('v1/project_details/query_gateway_dur', [ProjectDetailController::class, 'queryGatewayDurByProjectID']);

Route::middleware('auth:api')->get('v1/project_roles', [ProjectRoleController::class, 'index']);
Route::middleware('auth:api')->get('v1/project_roles/{id}', [ProjectRoleController::class, 'show']);
Route::middleware('auth:api')->post('v1/project_roles', [ProjectRoleController::class, 'store']);
Route::middleware('auth:api')->put('v1/project_roles/{id}', [ProjectRoleController::class, 'update']);

Route::middleware('auth:api')->get('v1/system_config', [SystemConfigController::class, 'index']);
Route::middleware('auth:api')->post('v1/system_config', [SystemConfigController::class, 'store']);
Route::middleware('auth:api')->get('v1/system_config/{name}', [SystemConfigController::class, 'getByName']);

Route::middleware('auth:api')->get('v1/rules', [RulesEngineManagementController::class, 'getRules']);

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
