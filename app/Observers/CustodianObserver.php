<?php

namespace App\Observers;

use App\Models\DecisionModel;
use App\Models\Custodian;
use App\Models\CustodianModelConfig;
use App\Models\ActionLog;
use App\Traits\ValidationManager;

class CustodianObserver
{
    use ValidationManager;
    public function created(Custodian $custodian): void
    {
        // New Custodian's need all Decision models adding to their accounts
        // as a default installation
        $decisionModels = DecisionModel::all();
        foreach ($decisionModels as $d) {
            CustodianModelConfig::updateOrCreate(
                [
                'decision_model_id' => $d->id,
                'custodian_id' => $custodian->id,
            ],
                [
                'active' => 1,
            ]
            );
        }

        foreach (Custodian::getDefaultActions() as $action) {
            ActionLog::firstOrCreate([
                'entity_id' => $custodian->id,
                'entity_type' => Custodian::class,
                'action' => $action,
            ], [
                'completed_at' => null,
            ]);
        }
    }
}
