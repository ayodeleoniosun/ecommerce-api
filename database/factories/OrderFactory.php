<?php

namespace Database\Factories;

use App\Domains\Common\Enum\PaymentEnum;
use App\Infrastructure\Models\Order;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => str::uuid(),
            'reference' => Str::random(),
            'user_id' => User::factory()->id,
            'product_id' => Product::factory()->id,
            'quantity' => 2,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => PaymentEnum::PENDING->value,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => PaymentEnum::PROCESSING->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => PaymentEnum::SUCCESS->value,
        ]);
    }
}
