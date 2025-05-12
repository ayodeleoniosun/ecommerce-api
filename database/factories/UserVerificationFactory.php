<?php

namespace Database\Factories;

use App\Application\Shared\Enum\UserStatusEnum;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserVerificationFactory extends Factory
{
    protected $model = UserVerification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'token' => hash('sha256', '12345'),
            'expires_at' => Carbon::now()->addHours(6),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => now(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatusEnum::INACTIVE->value,
        ]);
    }
}
