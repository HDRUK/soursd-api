<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\ActionLog;
use App\Models\Custodian;
use App\Models\Organisation;
use App\Models\ValidationLog;
use App\Models\ValidationCheck;
use App\Traits\ValidationManager;
use App\Enums\ValidationCheckAppliesTo;
use Carbon\Carbon;

class ValidationCheckObserver
{
    use ValidationManager;

    public function created(ValidationCheck $model): void
    {
        $custodianId = $model->custodian_id;
        if (!$custodianId) {
            return;
        }

        if ($model->applies_to === ValidationCheckAppliesTo::Organisation) {
            $organisationIds = Organisation::pluck('id');
            foreach ($organisationIds as $organisationId) {
                ValidationLog::firstOrCreate(
                    [
                        'entity_id' => $custodianId,
                        'entity_type' => Custodian::class,
                        'secondary_entity_id' => $organisationId,
                        'secondary_entity_type' => Organisation::class,
                        'validation_check_id' => $model->id
                    ],
                    [
                        'completed_at' => null,
                    ]
                );
            }
        } elseif ($model->applies_to === ValidationCheckAppliesTo::ProjectUser) {
            $projectIds = Project::pluck('id')->toArray();
            $this->updateCustodianProjectUserSingleValidationCheck(
                $projectIds,
                $model->id,
                null,
                $custodianId,
            );
        }
    }

    public function updated(ValidationCheck $model): void
    {
        $custodianId = $model->custodian_id;
        if (!$custodianId) {
            return;
        }

        ActionLog::where([
            'entity_id' => $custodianId,
            'entity_type' => Custodian::class,
            'action' => Custodian::ACTION_COMPLETE_CONFIGURATION,
        ])->whereNull('completed_at')
            ->update(['completed_at' => Carbon::now()]);
    }
}
