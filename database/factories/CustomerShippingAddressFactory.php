<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Shipping\Address\City;
use App\Infrastructure\Models\Shipping\Address\Country;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;
use App\Infrastructure\Models\Shipping\Address\State;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PickupStation>
 */
class CustomerShippingAddressFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = CustomerShippingAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'phone_number' => '08123456744',
            'user_id' => User::factory()->create()->id,
            'country_id' => Country::factory()->create()->id,
            'state_id' => State::factory()->create()->id,
            'city_id' => City::factory()->create()->id,
            'address' => 'Lagos, Nigeria',
        ];
    }
}
