<?php

namespace Database\Factories\Payment\Wallet;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'currency' => CurrencyEnum::NGN,
            'balance' => 0,
        ];
    }
}
