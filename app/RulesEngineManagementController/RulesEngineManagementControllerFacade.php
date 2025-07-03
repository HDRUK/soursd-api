<?php

namespace App\RulesEngineManagementController;

use Illuminate\Support\Facades\Facade;
use App\RulesEngineManagementController\RulesEngineManagementController;

class RulesEngineManagementControllerFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RulesEngineManagementController::class;
    }
}
