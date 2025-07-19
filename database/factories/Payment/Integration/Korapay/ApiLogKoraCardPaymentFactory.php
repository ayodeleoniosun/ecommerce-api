<?php

namespace Database\Factories\Payment\Integration\Korapay;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Payment\Integration\Korapay\ApiLogsKoraCardPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\TransactionKoraCardPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiLogsKoraCardPayment>
 */
class ApiLogKoraCardPaymentFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = ApiLogsKoraCardPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'transaction_id' => TransactionKoraCardPayment::factory()->create()->id,
        ];
    }
}
