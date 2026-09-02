<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Feature::truncate();

        // Seed features with global scope
        $globalFeatures = [
            ['name' => 'test-feature', 'value' => 'true', 'description' => 'This is a test feature.'],
            ['name' => 'test-feature-user-admin', 'value' => 'true', 'description' => 'This feature is enabled for admin users only.'],
            ['name' => 'sponsorship', 'value' => 'true', 'description' => 'sponsorship feature'],
            ['name' => 'christmas-banner', 'value' => 'false', 'description' => 'Enable the Christmas banner across the site.'],
            ['name' => 'EnterpriseSAMLSSOEnabled', 'value' => 'false', 'description' => 'Enable enterprise SAML SSO: admin approval workflow, Custodian/Organisation Admin self-service connection requests, and the domain-lookup sign-in step.'],
            ['name' => 'LinkedIdentitiesEnabled', 'value' => 'true', 'description' => 'Enable linking external identity providers (ORCID, GitHub) to a researcher account.'],
        ];

        foreach ($globalFeatures as $feature) {
            Feature::create([
                'name' => $feature['name'],
                'scope' => '__laravel_null',
                'value' => $feature['value'],
                'description' => $feature['description'],
            ]);
        }

        $this->command->newLine();
        $this->command->info('All feature flags seeded successfully!');
        $this->command->newLine();
    }
}
