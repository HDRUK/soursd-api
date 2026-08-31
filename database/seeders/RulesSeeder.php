<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use App\Models\DecisionModel;
use App\Models\CustodianModelConfig;

class RulesSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DecisionModel::truncate();
        CustodianModelConfig::truncate();

        Schema::enableForeignKeyConstraints();

        $rules = [
            [
                'name' => 'User location',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'location',
                    'sanctioned_countries' => [
                        'China',
                        'Russia',
                        'North Korea',
                    ],
                ]),
                'rule_class' => \App\Rules\Users\UKDataProtection::class,
                'description' => 'A User should be located in a country which adheres to equivalent data protection law.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'Training',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.trainings',
                    'expects' => 'NHS Research Secure Data Environment Training',
                ]),
                'rule_class' => \App\Rules\Users\Training::class,
                'description' => 'A User has completed the NHS Research Secure Data Environment training.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'Training',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.trainings',
                    'expects' => 'Safe Researcher Training',
                ]),
                'rule_class' => \App\Rules\Users\Training::class,
                'description' => 'A User has completed the ONS Accredited Researcher training.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'Training',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.trainings',
                    'expects' => 'Research, GDPR, and Confidentiality',
                ]),
                'rule_class' => \App\Rules\Users\Training::class,
                'description' => 'A User has completed the MRC GDPR training.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'User affiliation',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.affiliations',
                    'expects' => [],
                ]),
                'rule_class' => \App\Rules\Users\AffiliatedOrganisation::class,
                'description' => 'A User has been affiliated by a relevant, validated Organisation.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'ce_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has Cyber Essentials certification.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'ce_plus_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has Cyber Essentials Plus certification.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'iso_27001_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has ISO27001 certification.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'dsptk_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has DSPT certification.',
                'decision_model_type_id' => 1,
            ],
            [
                'name' => 'Identity',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.identity.idvt_success',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Users\IdentityVerification::class,
                'description' => 'A User has verified their identity via the Identity Verification Technology (IDVT).',
                'decision_model_type_id' => 2,
            ],
            [
                'name' => 'User location',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'location',
                    'sanctioned_countries' => ['China', 'Russia', 'North Korea'], // Example
                ]),
                'rule_class' => \App\Rules\Users\UKDataProtection::class,
                'description' => 'A User is located in a country which has UK equivalent data protection laws.',
                'decision_model_type_id' => 2,
            ],
            [
                'name' => 'Training',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.trainings',
                    'expects' => 'NHS Research Secure Data Environment Training',
                ]),
                'rule_class' => \App\Rules\Users\Training::class,
                'description' => 'A User has completed the NHS Research Secure Data Environment training.',
                'decision_model_type_id' => 2,
            ],
            [
                'name' => 'Training',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.trainings',
                    'expects' => 'ONS Accredited Researcher Training',
                ]),
                'rule_class' => \App\Rules\Users\Training::class,
                'description' => 'A User has completed the ONS Accredited Researcher training.',
                'decision_model_type_id' => 2,
            ],
            [
                'name' => 'Training',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.trainings',
                    'expects' => 'MRC GDPR Training',
                ]),
                'rule_class' => \App\Rules\Users\Training::class,
                'description' => 'A User has completed the MRC GDPR training.',
                'decision_model_type_id' => 2,
            ],
            // TODO - Add this rule back in when the rule is implemented
            // [
            //     'name' => 'SDE Network',
            //     'model_type' => \App\Models\User::class,
            //     'conditions' => json_encode([
            //         'path' => 'registry.agreements',
            //         'expects' => 'NHS Research Secure Data Environment Terms of Use',
            //     ]),
            //     'rule_class' => \App\Rules\Users\AgreementAccepted::class, // You may need to define this
            //     'description' => 'A User has signed the NHS Research Secure Data Environment Terms of Use.',
            //     'decision_model_type_id' => 2,
            // ],
            [
                'name' => 'User affiliation',
                'model_type' => \App\Models\User::class,
                'conditions' => json_encode([
                    'path' => 'registry.affiliations',
                    'expects' => [],
                ]),
                'rule_class' => \App\Rules\Users\AffiliatedOrganisation::class,
                'description' => 'A User has been affiliated by a relevant, validated Organisation.',
                'decision_model_type_id' => 2,
            ],
            [
                'name' => 'Sanctions',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'country',
                    'sanctioned_countries' => [
                        'China',
                        'Russia',
                        'North Korea',
                    ],
                ]),
                'rule_class' => \App\Rules\Organisations\SanctionsCheck::class,
                'description' => 'An Organisation is not on the UK sanctions list.',
                'decision_model_type_id' => 3,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'ce_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has Cyber Essentials certification.',
                'decision_model_type_id' => 3,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'ce_plus_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has Cyber Essentials Plus certification.',
                'decision_model_type_id' => 3,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'iso_27001_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has ISO27001 Accredited certification.',
                'decision_model_type_id' => 3,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'ce_or_iso_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has either a Cyber Essentials or ISO27001 Accredited certification.',
                'decision_model_type_id' => 3,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'ce_plus_or_iso_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has either a Cyber Essentials Plus or ISO27001 Accredited certification.',
                'decision_model_type_id' => 3,
            ],
            [
                'name' => 'Data security compliance',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'dsptk_certified',
                    'expects' => 1,
                ]),
                'rule_class' => \App\Rules\Organisations\DataSecurityCompliance::class,
                'description' => 'An Organisation has DSPT certification.',
                'decision_model_type_id' => 3,
            ],
            [
                'name' => 'Delegate/Key Contact',
                'model_type' => \App\Models\Organisation::class,
                'conditions' => json_encode([
                    'path' => 'delegates',
                    'expects' => [
                        'minimum' => 1,
                    ],
                ]),
                'rule_class' => \App\Rules\Organisations\DelegateCheck::class,
                'description' => 'An Organisation has at least one Delegate/Key Contact to affiliate Users.',
                'decision_model_type_id' => 3,
            ],


        ];

        foreach ($rules as $rule) {
            DecisionModel::create($rule);
        }

        $custodians = \App\Models\Custodian::all();
        $decisionModels = DecisionModel::all();

        foreach ($custodians as $custodian) {
            foreach ($decisionModels as $decisionModel) {
                CustodianModelConfig::firstOrCreate(
                    [
                    'custodian_id' => $custodian->id,
                    'decision_model_id' => $decisionModel->id,
                ],
                    [
                    'active' => 1,
                ]
                );
            }
        }
    }
}
