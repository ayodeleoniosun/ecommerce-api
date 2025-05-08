<?php

namespace Database\Factories;

use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class SellerFactory extends Factory
{
    use UtilitiesTrait;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'uuid' => self::generateUUID(),
            'phone_number' => fake()->phoneNumber,
            'office_address' => fake()->address,
            'business_name' => fake()->company(),
            'business_description' => fake()->text,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserEnum::PENDING->value,
            'verified_at' => null,
            'disabled_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserEnum::ACTIVE->value,
            'verified_at' => now(),
            'disabled_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserEnum::INACTIVE->value,
            'disabled_at' => now(),
        ]);
    }
}
