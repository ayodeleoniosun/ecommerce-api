<?php

namespace Database\Factories\Payment\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Wallet\WalletOrderPayment;
use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletOrderPaymentFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = WalletOrderPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_transaction_id' => WalletTransaction::factory()->create()->id,
            'order_payment_id' => OrderPayment::factory()->create()->id,
        ];
    }
}
