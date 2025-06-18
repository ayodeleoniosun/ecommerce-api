<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Shipping\Address\City;
use App\Infrastructure\Models\Shipping\Address\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'state_id' => State::factory()->create()->id,
            'name' => 'Ikeja',
        ];
    }
}
