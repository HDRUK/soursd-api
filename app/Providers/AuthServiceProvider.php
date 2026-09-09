<?php

namespace App\Providers;

use App\Models\Affiliation;
use App\Models\User;
use App\Models\Project;
use App\Models\Registry;
use App\Models\ActionLog;
use App\Models\Custodian;
use App\Models\CustodianUser;
use App\Models\CustodianModelConfig;
use App\Models\Organisation;
use App\Policies\UserPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RegistryPolicy;
use App\Policies\ActionLogPolicy;
use App\Policies\AffiliationPolicy;
use App\Policies\CustodianPolicy;
use App\Policies\CustodianUserPolicy;
use App\Policies\CustodianModelConfigPolicy;
use App\Policies\OrganisationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ActionLog::class => ActionLogPolicy::class,
        Custodian::class => CustodianPolicy::class,
        CustodianUser::class => CustodianUserPolicy::class,
        CustodianModelConfig::class => CustodianModelConfigPolicy::class,
        Organisation::class => OrganisationPolicy::class,
        Project::class => ProjectPolicy::class,
        Registry::class => RegistryPolicy::class,
        User::class => UserPolicy::class,
        Affiliation::class => AffiliationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //role based gate
        Gate::define('admin', function (User $user) {
            return $user->user_group === User::GROUP_ADMINS;
        });
    }
}
