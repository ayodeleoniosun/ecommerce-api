<?php

namespace Database\Factories\Vendor;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Auth\Enums\UserStatusEnum;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\Vendor\VendorBusinessInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VendorBusinessInformation>
 */
class VendorBusinessInformationFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = VendorBusinessInformation::class;

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
            'name' => fake()->name,
            'description' => fake()->text(),
            'registration_number' => (string) fake()->numberBetween(100000, 999999),
            'tax_identification_number' => (string) fake()->numberBetween(100000, 999999),
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
