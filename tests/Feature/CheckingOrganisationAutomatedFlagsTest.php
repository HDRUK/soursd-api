<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Custodian;
use App\Models\DecisionModel;
use App\Models\DecisionModelType;
use Illuminate\Console\Command;
use App\Models\DecisionModelLog;
use App\Models\CustodianHasProjectOrganisation;

class CheckingOrganisationAutomatedFlagsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->withUsers();
    }

    public function test_successfully_processes_organisations_for_all_custodians_for_decision_model()
    {
        $custIds = Custodian::query()
                        ->pluck('id')
                        ->toArray();

        $decisionModelTypes = DecisionModelType::where('name', DecisionModelType::ORG_VALIDATION_RULES)->first();
        $countDecisionModel = DecisionModel::where('decision_model_type_id', $decisionModelTypes->id)->count();

        $countExpected = 0;
        foreach ($custIds as $custId) {
            $organisationIds = CustodianHasProjectOrganisation::query()
                    ->where([
                        'custodian_id' => $custId,
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
                $countExpected = $countExpected + $countDecisionModel;
            }
        }

        $this->artisan('app:checking-organisation-automated-flags')->assertExitCode(Command::SUCCESS);

        $countResult = DecisionModelLog::where('model_type', DecisionModelLog::DECISION_MODEL_ORGANISATIONS)->count();

        $this->assertTrue($countExpected === $countResult);
    }

    public function test_successfully_processes_organisations_for_all_custodians()
    {
        $arrCustodiansUsers = [];
        $custIds = Custodian::query()
                        ->pluck('id')
                        ->toArray();

        foreach ($custIds as $custId) {
            $organisationIds = CustodianHasProjectOrganisation::query()
                    ->where([
                        'custodian_id' => $custId,
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
                $arrCustodiansUsers[] = [
                    'custodianId' => $custId,
                    'organisationId' => $organisationId,
                ];
            }
        }

        $command = $this->artisan('app:checking-organisation-automated-flags');
        foreach ($arrCustodiansUsers as $item) {
            $command->expectsOutput('checking rules for organisation id ' . $item['organisationId'] . ' and custodian id ' . $item['custodianId'] . ' :: done');
        }

        $command->assertExitCode(Command::SUCCESS);
    }

}
