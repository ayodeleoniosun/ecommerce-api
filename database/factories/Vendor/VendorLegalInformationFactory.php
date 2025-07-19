<?php

namespace Database\Factories\Vendor;

use App\Application\Shared\Enum\UserStatusEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\Vendor\VendorLegalInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VendorLegalInformation>
 */
class VendorLegalInformationFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = VendorLegalInformation::class;

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
            'fullname' => fake()->firstName,
            'email' => fake()->unique()->safeEmail(),
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
