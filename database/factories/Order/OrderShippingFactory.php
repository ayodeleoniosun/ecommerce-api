<?php

namespace Database\Factories\Order;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Shipping\Enums\DeliveryTypeEnum;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderShipping;
use App\Infrastructure\Models\Shipping\Address\City;
use App\Infrastructure\Models\Shipping\Address\Country;
use App\Infrastructure\Models\Shipping\Address\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderShipping>
 */
class OrderShippingFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = OrderShipping::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'order_id' => Order::factory()->create()->id,
            'country_id' => Country::factory()->create()->id,
            'state_id' => State::factory()->create()->id,
            'city_id' => City::factory()->create()->id,
            'delivery_type' => DeliveryTypeEnum::DOOR_DELIVERY->value,
            'delivery_address' => 'Lagos, Nigeria',
            'pickup_station_name' => 'pickup station lagos',
            'pickup_station_address' => 'Lagos, Nigeria',
            'pickup_station_contact_name' => fake()->firstName().' '.fake()->lastName(),
            'pickup_station_contact_phone_number' => fake()->phoneNumber(),
            'estimated_delivery_start_date' => now()->addDays(2),
            'estimated_delivery_end_date' => now()->addDays(7),
        ];
    }
}
