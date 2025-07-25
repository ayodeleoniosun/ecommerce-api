<?php

namespace Database\Factories\Payment\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Enums\WalletTransactionTypeEnum;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletTransactionFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = WalletTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory()->create()->id,
            'amount' => 0,
            'type' => WalletTransactionTypeEnum::DEBIT->value,
        ];
    }
}
