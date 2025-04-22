<?php

namespace Database\Factories;

use App\Application\Shared\Enum\UserEnum;
use App\Infrastructure\Models\SellerContactInformation;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SellerContactInformation>
 */
class SellerContactInformationFactory extends Factory
{
    protected $model = SellerContactInformation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => str::uuid(),
            'user_id' => User::factory()->create()->id,
            'name' => fake()->firstName,
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber,
            'country' => fake()->country,
            'state' => fake()->state,
            'city' => fake()->city,
            'address' => fake()->address,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserEnum::PENDING->value,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserEnum::ACTIVE->value,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserEnum::INACTIVE->value,
        ]);
    }
}
