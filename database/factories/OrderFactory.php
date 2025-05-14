<?php

namespace Database\Factories;

use App\Application\Shared\Enum\PaymentStatusEnum;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Infrastructure\Models\Order\Order>
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
            'user_id' => User::factory()->create()->id,
            'product_id' => Product::factory()->create()->id,
            'quantity' => 2,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatusEnum::PENDING->value,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatusEnum::PROCESSING->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatusEnum::SUCCESS->value,
        ]);
    }
}
