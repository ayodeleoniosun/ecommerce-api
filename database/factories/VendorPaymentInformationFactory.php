<?php

namespace Database\Factories;

use App\Application\Shared\Enum\UserStatusEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\Vendor\VendorPaymentInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Infrastructure\Models\Vendor\VendorPaymentInformation>
 */
class VendorPaymentInformationFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = VendorPaymentInformation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
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
            'status' => UserStatusEnum::PENDING->value,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatusEnum::ACTIVE->value,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatusEnum::INACTIVE->value,
        ]);
    }
}
