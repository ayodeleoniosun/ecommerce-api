<?php

namespace Database\Seeders;

use App\Infrastructure\Models\Address\City;
use App\Infrastructure\Models\Address\Country;
use App\Infrastructure\Models\Address\State;
use Kdabrow\SeederOnce\SeederOnce;

class CountryStateAndCitySeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/data/countries_states_cities.json'));
        $countries = json_decode($json, true);

        foreach ($countries as $country) {
            // insert or update countries
            $countryRecord = Country::updateOrCreate(
                ['code' => $country['iso2'], 'currency_code' => $country['currency']],
                [
                    'name' => strtolower($country['name']),
                    'code' => $country['iso2'],
                    'phone_code' => $country['phonecode'],
                    'currency_code' => $country['currency'],
                    'currency' => $country['currency_name'],
                    'currency_symbol' => $country['currency_symbol'],
                ],
            );

            // insert or update states
            if (isset($country['states'])) {
                foreach ($country['states'] as $state) {
                    $stateRecord = State::updateOrCreate(
                        ['code' => $state['state_code'], 'country_id' => $countryRecord->id],
                        [
                            'country_id' => $countryRecord->id,
                            'name' => strtolower($state['name']),
                            'code' => $state['state_code'],
                        ],
                    );

                    // insert or update cities
                    if (isset($state['cities'])) {
                        foreach ($state['cities'] as $city) {
                            City::updateOrCreate(
                                ['state_id' => $stateRecord->id, 'name' => strtolower($city['name'])],
                                [
                                    'state_id' => $stateRecord->id,
                                    'name' => strtolower($city['name']),
                                ],
                            );
                        }
                    }
                }
            }
        }
    }
}
