<?php

namespace Database\Seeders;

use Hash;
use App\Models\Custodian;
use App\Models\Permission;
use App\Models\CustodianUser;
use App\Models\DecisionModel;
use Illuminate\Database\Seeder;
use App\Models\WebhookEventTrigger;
use App\Models\CustodianModelConfig;
use Illuminate\Support\Facades\Schema;
use App\Models\CustodianWebhookReceiver;
use App\Models\CustodianUserHasPermission;

class CustodianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        Custodian::truncate();
        CustodianUser::truncate();

        CustodianModelConfig::truncate();

        Schema::enableForeignKeyConstraints();

        $webhookEventTriggers = WebhookEventTrigger::where('enabled', true)->get();

        foreach (config('speedi.custodians') as $custodian) {
            $i = Custodian::factory()->create([
                'name' => $custodian['name'],
                'contact_email' => $custodian['contact_email'],
                'enabled' => 1,
                'idvt_required' => fake()->randomElement([0, 1]),
            ]);

            $decisionModels = DecisionModel::all();

            foreach ($decisionModels as $d) {
                CustodianModelConfig::firstOrCreate(
                    [
                    'decision_model_id' => $d->id,
                    'custodian_id' => $i->id,
                ],
                    [
                    'active' => 1,
                ]
                );
            }

            for ($x = 0; $x < 2; $x++) {
                $iu = CustodianUser::factory()->create([
                    'first_name' => 'Custodian',
                    'last_name' => 'Admin',
                    'email' => 'custodian' . ($x + 1) . '@' . strtolower(str_replace(' ', '.', $custodian['name'])) . '.notreal',
                    'password' => Hash::make('t3mpP4ssword!'),
                    'provider' => '',
                    'keycloak_id' => '',
                    'custodian_id' => $i->id,
                ]);

                $perm = Permission::where('name', '=', 'CUSTODIAN_ADMIN')->select(['id'])->first();
                CustodianUserHasPermission::create([
                    'custodian_user_id' => $iu->id,
                    'permission_id' => $perm->id,
                ]);
            }

            // foreach ($webhookEventTriggers as $webhookEventTrigger) {
            //     CustodianWebhookReceiver::create([
            //         'custodian_id' => $i->id,
            //         'url' => 'https://webhook.site/4c812c72-3db1-4162-9160-5a798b52306c', // free webhook receiver
            //         'webhook_event' => $webhookEventTrigger->id,
            //     ]);
            // }
            CustodianWebhookReceiver::create([
                'custodian_id' => $i->id,
                'url' => 'https://webhook.site/4c812c72-3db1-4162-9160-5a798b52306c', // free webhook receiver
                'webhook_event' => 1,
            ]);

            CustodianWebhookReceiver::create([
                'custodian_id' => $i->id,
                'url' => 'https://webhook.site/4c812c72-3db1-4162-9160-5a798b52306c', // free webhook receiver
                'webhook_event' => 3,
            ]);

            CustodianWebhookReceiver::create([
                'custodian_id' => $i->id,
                'url' => 'https://webhook.site/4c812c72-3db1-4162-9160-5a798b52306c', // free webhook receiver
                'webhook_event' => 4,
            ]);
        }
    }
}
