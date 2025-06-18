<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Shipping\Address\Country;
use App\Infrastructure\Models\Shipping\Address\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<State>
 */
class StateFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = State::class;

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
            'name' => 'Lagos',
            'code' => '234',
        ];
    }
}
