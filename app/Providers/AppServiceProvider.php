<?php

namespace App\Providers;

use App\Models\File;
use App\Models\ONSFile;
use App\Models\Registry;
use App\Models\Custodian;
use App\Models\CustodianUser;
use App\Models\CustodianHasRule;
use App\Models\ProjectHasUser;
use App\Models\User;
use App\Models\UserHasDepartments;
use App\Models\Organisation;
use App\Models\OrganisationHasSubsidiary;
use App\Models\Affiliation;
use App\Models\RegistryHasAffiliation;
use App\Models\ProjectHasCustodian;
use App\Models\OrganisationHasCustodianApproval;
use App\Models\RegistryReadRequest;
use App\Observers\FileObserver;
use App\Observers\ONSFileObserver;
use App\Observers\RegistryObserver;
use App\Observers\CustodianObserver;
use App\Observers\CustodianHasRuleObserver;
use App\Observers\ProjectHasUserObserver;
use App\Observers\UserObserver;
use App\Observers\UserHasDepartmentsObserver;
use App\Observers\OrganisationObserver;
use App\Observers\OrganisationHasSubsidiaryObserver;
use App\Observers\CustodianUserObserver;
use App\Observers\AffiliationObserver;
use App\Observers\RegistryHasAffiliationObserver;
use App\Observers\RegistryHasTrainingObserver;
use App\Observers\ProjectHasCustodianObserver;
use App\Observers\OrganisationHasCustodianApprovalObserver;
use App\Observers\AuditModelObserver;
use App\Observers\RegistryReadRequestObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Event::listen('eloquent.*', function ($eventName, $payload) {
            $model = $payload[0] ?? null;

            if ($model instanceof Model) {
                App::make(AuditModelObserver::class)->handle($eventName, $model);
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        File::observe(FileObserver::class);
        ONSFile::observe(ONSFileObserver::class);
        Registry::observe(RegistryObserver::class);
        ProjectHasUser::observe(ProjectHasUserObserver::class);
        Custodian::observe(CustodianObserver::class);
        CustodianHasRule::observe(CustodianHasRuleObserver::class);
        CustodianUser::observe(CustodianUserObserver::class);
        User::observe(UserObserver::class);
        UserHasDepartments::observe(UserHasDepartmentsObserver::class);
        Organisation::observe(OrganisationObserver::class);
        OrganisationHasSubsidiary::observe(OrganisationHasSubsidiaryObserver::class);
        Affiliation::observe(AffiliationObserver::class);
        RegistryHasAffiliation::observe(RegistryHasAffiliationObserver::class);
        ProjectHasCustodian::observe(ProjectHasCustodianObserver::class);
        OrganisationHasCustodianApproval::observe(OrganisationHasCustodianApprovalObserver::class);
        RegistryReadRequest::observe(RegistryReadRequestObserver::class);
        // currently Training but is to be moved to RegistryHasTraining...
        // RegistryHasTraining::observe(RegistryHasTrainingObserver::class);
    }
}
