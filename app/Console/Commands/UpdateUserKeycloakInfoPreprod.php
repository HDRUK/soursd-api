<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class UpdateUserKeycloakInfoPreprod extends Command
{
    protected $signature = 'users:update-keycloak';
    protected $description = 'Update keycloak_id, t_and_c_agreed, and t_and_c_agreement_date for users';

    public function handle()
    {
        $usersToUpdate = [
            'admin.user@healthdataorganisation.com' => 'f812c289-525d-4c5a-96ee-2e142ff2d7d8',
            'admin.user@tandyenergyltd.com' => 'e7a48dcd-a809-4473-9f17-fbbe83b289b7',
            'admin.user@tobaccoeultd.com' => '909c8215-d131-48c2-a402-186832b56042',
            'annie.potts@ghostbusters.com' => 'f912ea18-ca56-40df-9b3c-3c7cb9d43e51',
            'bill.murray@ghostbusters.com' => '9d818c5c-a313-45a5-a923-46a86bb685a3',
            'box-admin@hdruk.ac.uk' => '04e80bfd-7cff-4e68-93fd-3b2e4a55d523',
            'custodian1@nhs.england.notreal' => '6008f2b4-e65c-416e-9a9a-280c3c0337f4',
            'custodian1@sail.databank.notreal' => '80ddaa91-2d15-4887-9aff-31477d90a458',
            'dan.ackroyd@ghostbusters.com' => '89e5017a-087b-4272-86b9-899f6ff5f083',
            'delegate.sponsor@healthdataorganisation.com' => 'c5cf3a78-b8da-4e96-9759-41abb8b44c59',
            'delegate.sponsor@tandyenergyltd.com' => '1ec7825a-9f6a-40e3-8b84-a849f5672739',
            'delegate.sponsor@tobaccoeultd.com' => '0e40d878-eda8-42f4-846c-baa977d3fa8f',
            'harold.ramis@ghostbusters.com' => 'dc968eed-5086-486e-b749-64ec0b6ad14e',
            'jennifer.runyon@ghostbusters.com' => 'ad18e446-39e0-4612-bb23-0d041aa89f99',
            'organisation.owner@healthdataorganisation.com' => '19ec2bed-b1f2-4c6b-b10e-e7376a02b6d8',
            'organisation.owner@tandyenergyltd.com' => 'ecb45169-14cb-49a0-9295-ed11d412ff84',
            'sigourney.weaver@ghostbusters.com' => '1aed8010-e25d-4ed3-abaf-4e6a91f3e6c7',
            'tobacco.dave@dodgydomain.com' => '5c352b46-a377-4929-962c-94836c72ba6b',
            'tobacco.frank@tobaccoeultd.com' => '297a7b47-f3bb-453d-bb9c-f97e906df838',
            'tobacco.john@dodgydomain.com' => '12453517-2baf-40f3-88a0-16f9873d702b',
        ];

        $updated = 0;

        foreach ($usersToUpdate as $email => $keycloakId) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->keycloak_id = $keycloakId;
                $user->t_and_c_agreed = 1;
                $user->t_and_c_agreement_date = Carbon::now();
                $user->save();

                $this->info("Updated: {$email}");
                $updated++;
            } else {
                $this->warn("User not found: {$email}");
            }
        }

        $this->info("Update complete. Total users updated: {$updated}");
    }
}
