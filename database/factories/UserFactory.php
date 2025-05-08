<?php

namespace Database\Factories;

use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Enum\UserTypeEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    use UtilitiesTrait;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'firstname' => fake()->firstName,
            'lastname' => fake()->firstName,
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('Ayodele@2025'),
            'remember_token' => Str::random(10),
            'type' => UserTypeEnum::CUSTOMER->value,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserEnum::ACTIVE->value,
            'email_verified_at' => now(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserEnum::INACTIVE->value,
        ]);
    }
}
