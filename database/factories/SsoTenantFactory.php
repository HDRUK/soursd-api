<?php

namespace Database\Factories;

use App\Models\SsoTenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SsoTenant>
 */
class SsoTenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name,
            'idp_alias' => Str::slug($name) . '-' . Str::random(6),
            'metadata_url' => $this->faker->url(),
            'entity_id' => $this->faker->url(),
            'metadata_imported_at' => $this->faker->dateTimeThisYear(),
            'enabled' => true,
            'status' => SsoTenant::STATUS_APPROVED,
        ];
    }
}
