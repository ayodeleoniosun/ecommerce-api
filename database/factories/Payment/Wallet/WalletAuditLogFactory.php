<?php

namespace Database\Factories\Payment\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Models\Payment\Wallet\WalletAuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletAuditLogFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = WalletAuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory()->create()->id,
            'previous_balance' => 100,
            'new_balance' => 200,
        ];
    }
}
