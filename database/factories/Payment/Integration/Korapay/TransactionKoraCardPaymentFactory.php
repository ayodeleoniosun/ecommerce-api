<?php

namespace Database\Factories\Payment\Integration\Korapay;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Enums\AuthModelEnum;
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
            'order_payment_reference' => OrderPayment::factory()->create()->reference,
            'reference' => self::generateRandomCharacters(),
            'currency' => 'NGN',
            'amount' => 4000,
            'gateway_transaction_reference' => 'KPY-1234',
            'auth_model' => AuthModelEnum::PIN->value,
        ];
    }
}
