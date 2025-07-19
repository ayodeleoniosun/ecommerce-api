<?php

namespace Database\Factories\Vendor;

use App\Application\Shared\Enum\UserStatusEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\Vendor\VendorContactInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VendorContactInformation>
 */
class VendorContactInformationFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = VendorContactInformation::class;

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
