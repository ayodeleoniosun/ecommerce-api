<?php

namespace Database\Factories\Payment;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Enums\GatewayEnum;
use App\Infrastructure\Models\Payment\Gateway;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gateway>
 */
class GatewayFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = Gateway::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'slug' => GatewayEnum::KORAPAY->value,
            'name' => ucfirst(GatewayEnum::KORAPAY->value),
        ];
    }
}
