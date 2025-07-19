<?php

namespace Database\Factories\User;

use App\Infrastructure\Models\User\PasswordResetToken;
use App\Infrastructure\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class PasswordResetTokenFactory extends Factory
{
    protected $model = PasswordResetToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => User::factory()->create()->email,
            'token' => Hash::make('6baad6f126fa53233f5120dd32225d4a9eeaea26dce58789f0b3b6efcdb0dadb'),
            'created_at' => Carbon::now(),
        ];
    }
}
