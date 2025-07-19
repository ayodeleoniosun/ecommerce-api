<?php

namespace Database\Factories\Shipping\Address;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Constants\Currencies;
use App\Infrastructure\Models\Shipping\Address\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'name' => fake()->country(),
            'code' => fake()->countryCode(),
            'phone_code' => '234',
            'currency_code' => Currencies::NGN->value,
            'currency' => 'Nigerian Naira',
            'currency_symbol' => '₦',
        ];
    }
}
