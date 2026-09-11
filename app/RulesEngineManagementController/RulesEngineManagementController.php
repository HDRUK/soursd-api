<?php

namespace App\RulesEngineManagementController;

use Auth;
use App\Models\User;
use App\Models\CustodianUser;
use App\Models\DecisionModel;
use App\Models\CustodianModelConfig;
use App\Models\DecisionModelType;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method static \Illuminate\Database\Eloquent\Collection|null loadCustodianRules(array $validationType, ?int $custId)
 */
class RulesEngineManagementController
{
    public static function getCustodianKeyFromHeaders(): string
    {
        $obj = json_decode(Auth::token(), true);

        if (isset($obj['sub'])) {
            return $obj['sub'];
        }

        return '';
    }

    public static function determineUserCustodian(): mixed
    {
        $key = self::getCustodianKeyFromHeaders();
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

    public static function loadCustodianRules(array $validationType, ?int $custId): ?Collection
    {
        $custodianId = $custId ? $custId : self::determineUserCustodian();
        if (!$custodianId) {
            return null;
        }

        $decisionModelTypeIds = [];

        if (filled($validationType)) {
            $decisionModelTypeIds = DecisionModelType::whereIn('name', $validationType)->pluck('id');
        } else {
            $decisionModelTypeIds = DecisionModelType::whereIn('name', [
                DecisionModelType::USER_VALIDATION_RULES,
                DecisionModelType::ORG_VALIDATION_RULES
            ])->pluck('id');
        }

        $modelConfig = CustodianModelConfig::where([
            'custodian_id' => $custodianId,
            'active' => 1,
        ])->select('decision_model_id')
        ->pluck('decision_model_id');

        if (!$modelConfig) {
            return null;
        }

        $activeModels = DecisionModel::whereIn('id', $modelConfig)->whereIn('decision_model_type_id', $decisionModelTypeIds)->get();
        if (!$activeModels) {
            return null;
        }

        return $activeModels;
    }
}
