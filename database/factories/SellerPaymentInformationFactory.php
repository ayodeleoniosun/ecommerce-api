<?php

namespace Database\Factories;

use App\Application\Shared\Enum\UserEnum;
use App\Infrastructure\Models\SellerPaymentInformation;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SellerPaymentInformation>
 */
class SellerPaymentInformationFactory extends Factory
{
    protected $model = SellerPaymentInformation::class;

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
            'account_name' => fake()->name,
            'account_number' => fake()->numberBetween(100000, 999999),
            'bank_code' => fake()->numberBetween(100, 999),
            'bank_name' => fake()->name,
            'swift_code' => fake()->numberBetween(10000, 99999),
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
