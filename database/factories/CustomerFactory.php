<?php

namespace Database\Factories;

use App\Infrastructure\Models\User;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Model>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->id,
            'uuid' => str::uuid(),
            'phone_number' => fake()->phoneNumber,
            'home_address' => fake()->address,
        ];
    }
}
