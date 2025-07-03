<?php

namespace App\Providers;

use DB;
use App\Models\File;
use App\Models\User;
use App\Models\ONSFile;
use App\Models\Registry;
use App\Models\Custodian;
use App\Models\Affiliation;
use App\Models\Organisation;
use App\Models\CustodianUser;
use App\Models\ProjectHasUser;
use App\Observers\FileObserver;
use App\Observers\UserObserver;
use App\Models\CustodianHasRule;
use App\Models\UserHasDepartments;
use App\Observers\ONSFileObserver;
use Laravel\Octane\Facades\Octane;
use App\Models\ProjectHasCustodian;
use App\Models\RegistryReadRequest;
use App\Observers\RegistryObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Observers\CustodianObserver;
use App\Observers\AuditModelObserver;
use Illuminate\Support\Facades\Event;
use App\Models\ProjectHasOrganisation;
use App\Observers\AffiliationObserver;
use App\Observers\OrganisationObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Observers\CustodianUserObserver;
use App\Models\OrganisationHasSubsidiary;
use App\Observers\ProjectHasUserObserver;
use Laravel\Octane\OctaneServiceProvider;
use App\Models\CustodianHasValidationCheck;
use App\Observers\CustodianHasRuleObserver;
use App\Observers\UserHasDepartmentsObserver;
use App\Observers\ProjectHasCustodianObserver;
use App\Observers\RegistryReadRequestObserver;
use App\Models\CustodianHasProjectOrganisation;
use App\Observers\ProjectHasOrganisationObserver;
use App\Observers\OrganisationHasSubsidiaryObserver;
use Illuminate\Foundation\Http\Events\RequestHandled;
use App\Observers\CustodianHasValidationCheckObserver;
use App\Observers\CustodianHasProjectOrganisationObserver;

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

        // if (app()->providerIsLoaded(OctaneServiceProvider::class)) {
        //     Octane::tick('gc', function () {
        //         // Run garbage collection after every request
        //         gc_collect_cycles();

        //         // Optional: Log GC trigger
        //         Log::info('Octane GC triggered', [
        //             'memory_usage' => memory_get_usage(true),
        //             'peak_memory'  => memory_get_peak_usage(true)
        //         ]);

        //         // Clean up persistent DB connections
        //         DB::disconnect();
        //     })->seconds(5);
        // }

        Event::listen(RequestHandled::class, function () {
            // Safe to use Laravel services here
            gc_collect_cycles();

            Log::info('Garbage collection after request', [
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
            ]);
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
        Affiliation::observe(AffiliationObserver::class);
        ProjectHasCustodian::observe(ProjectHasCustodianObserver::class);
        ProjectHasOrganisation::observe(ProjectHasOrganisationObserver::class);
        CustodianHasProjectOrganisation::observe(CustodianHasProjectOrganisationObserver::class);
        CustodianHasValidationCheck::observe(CustodianHasValidationCheckObserver::class);
        RegistryReadRequest::observe(RegistryReadRequestObserver::class);
        // currently Training but is to be moved to RegistryHasTraining...
        // RegistryHasTraining::observe(RegistryHasTrainingObserver::class);
    }
}
