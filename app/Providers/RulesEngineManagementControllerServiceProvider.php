<?php

namespace App\Providers;

use App\RulesEngineManagementController\RulesEngineManagementController;
use Illuminate\Support\ServiceProvider;

class RulesEngineManagementControllerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RulesEngineManagementController::class, function ($app) {
            return new RulesEngineManagementController();
        });
    }

    public function boot(): void
    {
        //
    }
}
