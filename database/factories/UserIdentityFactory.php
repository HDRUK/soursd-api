<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserIdentity>
 */
class UserIdentityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider' => fake()->randomElement(['orcid', 'github']),
            'provider_user_id' => fake()->uuid(),
            'provider_username' => fake()->userName(),
            'claims' => null,
            'linked_at' => now(),
        ];
    }
}
