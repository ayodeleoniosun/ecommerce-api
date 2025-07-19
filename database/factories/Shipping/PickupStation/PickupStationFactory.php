<?php

namespace Database\Factories\Shipping\PickupStation;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Shipping\Address\City;
use App\Infrastructure\Models\Shipping\Address\Country;
use App\Infrastructure\Models\Shipping\Address\State;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PickupStation>
 */
class PickupStationFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = PickupStation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'country_id' => Country::factory()->create()->id,
            'state_id' => State::factory()->create()->id,
            'city_id' => City::factory()->create()->id,
            'name' => 'pickup station',
            'address' => 'Lagos, Nigeria',
            'contact_phone_number' => fake()->phoneNumber(),
            'contact_name' => fake()->firstName().' '.fake()->lastName(),
        ];
    }
}
