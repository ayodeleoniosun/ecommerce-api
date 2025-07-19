<?php

namespace Database\Factories\Payment;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Enums\PaymentCategoryEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Infrastructure\Models\Payment\Gateway;
use App\Infrastructure\Models\Payment\GatewayConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GatewayConfiguration>
 */
class GatewayConfigurationFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = GatewayConfiguration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'type' => PaymentTypeEnum::CARD->value,
            'category' => PaymentCategoryEnum::COLLECTION->value,
            'currency' => 'NGN',
            'gateway_id' => Gateway::factory()->create()->id,
            'settings' => json_encode([
                'transaction_limit' => [
                    'max' => 1000000,
                    'min' => 100,
                ],
            ]),
        ];
    }
}
