<?php

namespace App\Console\Commands\Oneoff;

use Exception;
use Illuminate\Console\Command;
use App\Models\CustodianModelConfig;

class REG2496_20260817_RemoveDuplicatedCustodianModelConfigs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oneoff:remove-duplicated-custodian-model-configs {--dryrun}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        try {
            $dryrun = $this->option('dryrun');
            if ($dryrun) {
                var_dump('Running in dry run mode - no changes will be made to the database.');
            }

            $uniqueData = \DB::select("
                WITH cte AS (
                    SELECT *, ROW_NUMBER() OVER (PARTITION BY custodian_id, entity_model_id ORDER BY id) rn
                    FROM custodian_model_configs
                )
                SELECT id
                FROM cte
                WHERE rn > 1");
            $uniqueIds = array_column($uniqueData, 'id');

            var_dump("IDs to be deleted:\n", $uniqueIds);

            if (!$dryrun) {
                CustodianModelConfig::destroy($uniqueIds);
                var_dump('Finished deleting duplicate records');
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            $this->newLine();
            $this->line('Stack trace:');
            $this->line($e->getTraceAsString());

            var_dump('Command failed', [
                'message' => $e->getMessage()
            ]);

            return Command::FAILURE;
        }
    }
}
