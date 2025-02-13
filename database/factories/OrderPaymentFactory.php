<?php

namespace Database\Factories;

use App\Domains\Common\Enum\PaymentEnum;
use App\Infrastructure\Models\Order;
use App\Infrastructure\Models\OrderPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Infrastructure\Models\OrderPayment>
 */
class OrderPaymentFactory extends Factory
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
            'order_id' => Order::factory()->id,
            'reference' => Str::random(),
            'amount_paid' => 1000,
            'order_amount' => 950,
            'fee' => 25,
            'vat' => 25,
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
            'processor' => 'flutterwave',
            'processor_reference' => Str::random(),
            'completed_at' => now(),
        ]);
    }
}
