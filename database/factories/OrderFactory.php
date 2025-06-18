<?php

namespace Database\Factories;

use App\Application\Shared\Enum\OrderStatusEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'reference' => Str::random(),
            'user_id' => User::factory()->create()->id,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::PENDING->value,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::PROCESSING->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::COMPLETED->value,
        ]);
    }
}
