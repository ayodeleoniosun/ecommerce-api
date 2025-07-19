<?php

namespace Database\Factories\Payment\Integration\Korapay;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\TransactionKoraCardPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionKoraCardPayment>
 */
class TransactionKoraCardPaymentFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = TransactionKoraCardPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'order_payment_id' => OrderPayment::factory()->create()->id,
            'reference' => self::generateRandomCharacters(),
            'currency' => 'NGN',
            'amount' => 4000,
        ];
    }
}
