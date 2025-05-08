<?php

namespace Database\Factories;

use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\SellerBusinessInformation;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SellerBusinessInformation>
 */
class SellerBusinessInformationFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = SellerBusinessInformation::class;

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
