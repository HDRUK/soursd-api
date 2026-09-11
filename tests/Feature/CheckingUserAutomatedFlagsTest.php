<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Custodian;
use App\Models\DecisionModel;
use App\Models\DecisionModelType;
use Illuminate\Console\Command;
use App\Models\DecisionModelLog;
use App\Models\CustodianHasProjectUser;

class CheckingUserAutomatedFlagsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->withUsers();
    }

    public function test_successfully_processes_users_for_all_custodians_for_decision_model()
    {
        $custIds = Custodian::query()
                        ->pluck('id')
                        ->toArray();

        $decisionModelTypes = DecisionModelType::where('name', DecisionModelType::USER_VALIDATION_RULES)->first();
        $countDecisionModel = DecisionModel::where('decision_model_type_id', $decisionModelTypes->id)->count();

        $countExpected = 0;
        foreach ($custIds as $custId) {
            $userIds = CustodianHasProjectUser::query()
                        ->where([
                            'custodian_id' => $custId,
                        ])
                        ->with([
                            'projectHasUser.registry.user'
                        ])
                        ->get()
                        ->map(fn ($item) => $item->projectHasUser->registry?->user?->id)
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();

            foreach ($userIds as $userId) {
                $countExpected = $countExpected + $countDecisionModel;
            }
        }

        $this->artisan('app:checking-user-automated-flags')->assertExitCode(Command::SUCCESS);

        $countResult = DecisionModelLog::where('model_type', DecisionModelLog::DECISION_MODEL_USERS)->count();

        $this->assertTrue($countExpected === $countResult);
    }

    public function test_successfully_processes_users_for_all_custodians()
    {
        $arrCustodiansUsers = [];
        $custIds = Custodian::query()
                        ->pluck('id')
                        ->toArray();

        foreach ($custIds as $custId) {
            $userIds = CustodianHasProjectUser::query()
                        ->where([
                            'custodian_id' => $custId,
                        ])
                        ->with([
                            'projectHasUser.registry.user'
                        ])
                        ->get()
                        ->map(fn ($item) => $item->projectHasUser->registry?->user?->id)
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();

            foreach ($userIds as $userId) {
                $arrCustodiansUsers[] = [
                    'custodianId' => $custId,
                    'userId' => $userId,
                ];
            }
        }

        $command = $this->artisan('app:checking-user-automated-flags');
        foreach ($arrCustodiansUsers as $item) {
            $command->expectsOutput('checking rules for user id ' . $item['userId'] . ' and custodian id ' . $item['custodianId'] . ' :: done');
        }

        $command->assertExitCode(Command::SUCCESS);
    }

    public function test_application_process_user_update()
    {
        $decisionModelTypes = DecisionModelType::where('name', DecisionModelType::USER_VALIDATION_RULES)->first();
        $decisionModelLocation = DecisionModel::where([
            'decision_model_type_id' => $decisionModelTypes->id,
            'name' => 'User location',
        ])->first();

        $custodianId = null;
        $userId = null;
        $custIds = Custodian::query()
                        ->pluck('id')
                        ->toArray();

        foreach ($custIds as $custId) {
            $uIds = CustodianHasProjectUser::query()
                        ->where([
                            'custodian_id' => $custId,
                        ])
                        ->with([
                            'projectHasUser.registry.user'
                        ])
                        ->get()
                        ->map(fn ($item) => $item->projectHasUser->registry?->user?->id)
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();

            foreach ($uIds as $uId) {
                $userId = $uId;
                $custodianId = $custId;
                break;
            }

            if ($userId && $custodianId) {
                break;
            }
        }
        User::where('id', $userId)->update([
            'location' => null,
        ]);

        $this->artisan('app:checking-user-automated-flags')->assertExitCode(Command::SUCCESS);

        $intialDecisionModelLog = DecisionModelLog::where([
            'decision_model_id' => $decisionModelLocation->id,
            'custodian_id' => $custodianId,
            'subject_id' => $userId,
            'model_type' => DecisionModelLog::DECISION_MODEL_USERS,
        ])->first();

        $this->assertFalse((bool)$intialDecisionModelLog->status);

        User::where('id', $userId)->update([
            'location' => 'United Kingdom',
        ]);

        $this->artisan('app:checking-user-automated-flags')->assertExitCode(Command::SUCCESS);

        $updatedDecisionModelLog = DecisionModelLog::where([
            'decision_model_id' => $decisionModelLocation->id,
            'custodian_id' => $custodianId,
            'subject_id' => $userId,
            'model_type' => DecisionModelLog::DECISION_MODEL_USERS,
        ])->first();

        $this->assertTrue((bool)$updatedDecisionModelLog->status);
    }

}
