<?php

namespace App\RulesEngineManagementController;

use Auth;
use App\Models\User;
use App\Models\CustodianUser;
use App\Models\DecisionModel;
use App\Models\CustodianModelConfig;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method static \Illuminate\Database\Eloquent\Collection|null loadCustodianRules(\Illuminate\Http\Request $request)
 */
class RulesEngineManagementController
{
    public function getCustodianKeyFromHeaders(): string
    {
        $obj = json_decode(Auth::token(), true);

        if (isset($obj['sub'])) {
            return $obj['sub'];
        }

        return '';
    }

    public function determineUserCustodian(): mixed
    {
        $key = $this->getCustodianKeyFromHeaders();
        $user = User::where('keycloak_id', $key)->first();

        if (!$user || $user->user_group !== 'CUSTODIANS') {
            return null;
        }

        $custodianId = CustodianUser::where('id', $user->custodian_user_id)
            ->select('custodian_id')
            ->pluck('custodian_id');

        if (!$custodianId) {
            return null;
        }

        return $custodianId;
    }

    public function loadCustodianRules(Request $request): ?Collection
    {
        $custodianId = $this->determineUserCustodian();
        if (!$custodianId) {
            return null;
        }

        $modelConfig = CustodianModelConfig::where([
            'custodian_id' => $custodianId,
            'active' => 1,
        ])->select('entity_model_id')
        ->pluck('entity_model_id');

        if (!$modelConfig) {
            return null;
        }

        $activeModels = DecisionModel::whereIn('id', $modelConfig)->get();
        if (!$activeModels) {
            return null;
        }

        return $activeModels;
    }
}
