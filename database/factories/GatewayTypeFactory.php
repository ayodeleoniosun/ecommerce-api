<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Constants\PaymentCategoryEnum;
use App\Domain\Payment\Constants\PaymentTypeEnum;
use App\Infrastructure\Models\Payment\Gateway;
use App\Infrastructure\Models\Payment\GatewayType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GatewayType>
 */
class GatewayTypeFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = GatewayType::class;

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
            'primary_gateway_id' => Gateway::factory()->create()->id,
            'secondary_gateway_id' => Gateway::factory()->create()->id,
        ];
    }
}
