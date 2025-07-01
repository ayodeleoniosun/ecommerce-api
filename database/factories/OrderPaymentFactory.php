<?php

namespace Database\Factories;

use App\Application\Shared\Enum\PaymentStatusEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<OrderPayment>
 */
class OrderPaymentFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = OrderPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => str::uuid(),
            'order_id' => Order::factory()->create()->id,
            'reference' => Str::random(),
            'amount_charged' => 12000,
            'order_amount' => 10000,
            'currency' => 'NGN',
            'delivery_amount' => 2000,
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
            'processor' => 'korapay',
            'processor_reference' => Str::random(),
            'completed_at' => now(),
        ]);
    }
}
