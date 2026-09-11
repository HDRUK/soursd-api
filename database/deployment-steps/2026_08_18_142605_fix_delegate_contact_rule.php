<?php

use App\DeploymentSteps\DeploymentStep;
use App\Models\DecisionModel;

/**
 *
 */
return new class () extends DeploymentStep {
    public function handle(): void
    {
        try {
            $delegateRule = DecisionModel::where('name', 'Delegate/Key Contact')->update([
                'conditions' => json_encode([
                    "path" => "delegates",
                    "expects" => [
                        "minimum" => 1
                    ]
                ])
            ]);

            $this->info("Updated delegate rule");
        } catch (\Throwable $e) {
            $this->warn("Delegate rule could not be updated: {$e->getMessage()}");
        }
    }
};
