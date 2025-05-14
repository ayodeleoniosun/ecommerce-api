<?php

namespace Database\Seeders;

use App\Infrastructure\Models\Order\City;
use App\Infrastructure\Models\Order\Country;
use App\Infrastructure\Models\Order\State;
use Illuminate\Database\Seeder;

class CountryStateAndCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/data/countries_states_cities.json'));
        $countries = json_decode($json, true);

        foreach ($countries as $country) {
            $countryRecord = Country::updateOrCreate(
                ['code' => $country['iso2']],
                [
                    'name' => $country['name'],
                    'code' => $country['iso2'],
                    'phone_code' => $country['phonecode'],
                    'currency_code' => $country['currency'],
                    'currency' => $country['currency_name'],
                    'currency_symbol' => $country['currency_symbol'],
                ],
            );

            if (isset($country['states'])) {
                foreach ($country['states'] as $state) {
                    $stateRecord = State::updateOrCreate(
                        ['code' => $state['state_code'], 'country_id' => $countryRecord->id],
                        [
                            'country_id' => $countryRecord->id,
                            'name' => $state['name'],
                            'code' => $state['state_code'],
                        ],
                    );

                    if (isset($state['cities'])) {
                        foreach ($state['cities'] as $city) {
                            City::updateOrCreate(
                                ['state_id' => $stateRecord->id],
                                [
                                    'state_id' => $stateRecord->id,
                                    'name' => $city['name'],
                                ],
                            );
                        }
                    }
                }
            }
        }
    }
}
