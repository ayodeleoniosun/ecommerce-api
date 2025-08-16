<?php

namespace Database\Seeders;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Shipping\Address\Country;
use App\Infrastructure\Models\Shipping\Address\State;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;

class CountryStateAndCitySeeder extends SeederOnce
{
    use UtilitiesTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/data/countries_states_cities.json'));
        $countriesData = json_decode($json, true);

        $countries = [];
        $now = now();

        foreach ($countriesData as $country) {
            $countries[] = [
                'uuid' => self::generateUUID(),
                'name' => strtolower($country['name']),
                'code' => $country['iso2'],
                'phone_code' => $country['phonecode'],
                'currency_code' => $country['currency'],
                'currency' => $country['currency_name'],
                'currency_symbol' => $country['currency_symbol'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('countries')->insert($countries);

        $states = [];
        $countryIds = Country::pluck('id', 'code')->toArray();

        foreach ($countriesData as $country) {
            $countryId = $countryIds[$country['iso2']] ?? null;

            if (! $countryId || empty($country['states'])) {
                continue;
            }

            foreach ($country['states'] as $state) {
                $states[] = [
                    'uuid' => self::generateUUID(),
                    'country_id' => $countryId,
                    'name' => strtolower($state['name']),
                    'code' => $state['id'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('states')->insert($states);

        $cities = [];
        $stateIds = State::pluck('id', 'code')->toArray();

        foreach ($countriesData as $country) {
            $countryId = $countryIds[$country['iso2']] ?? null;

            if (! $countryId || empty($country['states'])) {
                continue;
            }

            foreach ($country['states'] as $state) {
                $stateId = $stateIds[$state['id']] ?? null;

                if (! $stateId || empty($state['cities'])) {
                    continue;
                }

                foreach ($state['cities'] as $city) {
                    $cities[] = [
                        'uuid' => self::generateUUID(),
                        'state_id' => $stateId,
                        'name' => strtolower($city['name']),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        $batchSize = 5000;

        foreach (array_chunk($cities, $batchSize) as $chunk) {
            DB::table('cities')->insert($chunk);
        }
    }
}
