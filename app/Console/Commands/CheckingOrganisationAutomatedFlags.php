<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Custodian;
use App\Models\Organisation;
use App\Models\DecisionModelType;
use Illuminate\Console\Command;
use App\Models\DecisionModelLog;
use App\Services\DecisionEvaluatorService;
use App\Models\CustodianHasProjectOrganisation;
use Illuminate\Support\Facades\Log;

class CheckingOrganisationAutomatedFlags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:checking-organisation-automated-flags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command checking organisation automated flags';

    protected $decisionEvaluator = null;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $custodianIds = Custodian::query()
                ->pluck('id')
                ->toArray();

            foreach ($custodianIds as $custodianId) {
                $organisationIds = CustodianHasProjectOrganisation::query()
                    ->where([
                        'custodian_id' => $custodianId,
                    ])
                    ->with([
                        'projectOrganisation.organisation'
                    ])
                    ->get()
                    ->map(fn ($item) => $item->projectOrganisation?->organisation?->id)
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                foreach ($organisationIds as $organisationId) {
                    $this->checkOrganisationById($custodianId, $organisationId);
                    $this->info("checking rules for organisation id {$organisationId} and custodian id {$custodianId} :: done");
                }
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            $this->newLine();
            $this->line('Stack trace:');
            $this->line($e->getTraceAsString());

            Log::error('Command checking organisation automated flags', [
                'message' => $e->getMessage()
            ]);

            return Command::FAILURE;
        }
    }

    public function checkOrganisationById(int $cId, int $oId)
    {
        $this->decisionEvaluator = new DecisionEvaluatorService([DecisionModelType::ORG_VALIDATION_RULES], $cId);

        $organisation = Organisation::with([
                            'departments',
                            'subsidiaries',
                            'permissions',
                            'ceExpiryEvidence',
                            'cePlusExpiryEvidence',
                            'isoExpiryEvidence',
                            'dsptkExpiryEvidence',
                            'charities',
                            'registries',
                            'registries.user',
                            'registries.user.permissions',
                            'sector',
                            'files',
                        ])->where('id', $oId)->first();
        $rules = $this->decisionEvaluator->evaluate($organisation);

        foreach ($rules as $rule) {
            DecisionModelLog::updateOrCreate([
                'decision_model_id' => $rule['ruleId'],
                'custodian_id' => $cId,
                'subject_id' => $oId,
                'model_type' => DecisionModelLog::DECISION_MODEL_ORGANISATIONS,
            ], [
                'status' => $rule['status'],
            ]);
        }
    }
}
