<?php

namespace Database\Factories\Payment\Integration\Flutterwave;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\ApiLogsFlutterwaveCardPayment;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\TransactionFlutterwaveCardPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\ApiLogsKoraCardPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiLogsKoraCardPayment>
 */
class ApiLogFlutterwaveCardPaymentFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = ApiLogsFlutterwaveCardPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'transaction_id' => TransactionFlutterwaveCardPayment::factory()->create()->id,
        ];
    }
}
