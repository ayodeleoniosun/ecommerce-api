<?php

namespace Database\Factories\User;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Auth\Enums\UserStatusEnum;
use App\Infrastructure\Models\User\User;
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
            'status' => UserStatusEnum::PENDING->value,
            'verified_at' => null,
            'disabled_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatusEnum::ACTIVE->value,
            'verified_at' => now(),
            'disabled_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatusEnum::INACTIVE->value,
            'disabled_at' => now(),
        ]);
    }
}
