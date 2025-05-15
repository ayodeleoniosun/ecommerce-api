<?php

namespace App\Domain\Shipping\Requests\PickupStation;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\Address\City;
use App\Infrastructure\Models\Address\Country;
use App\Infrastructure\Models\Address\State;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CreatePickupStationRequest extends FormRequest
{
    use OverrideDefaultValidationMethodTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'country_id' => ['required', 'string', 'exists:countries,uuid'],
            'state_id' => ['required', 'string', 'exists:states,uuid'],
            'city_id' => ['required', 'string', 'exists:cities,uuid'],
            'merged_country_id' => ['required', 'integer'],
            'merged_state_id' => ['required', 'integer'],
            'merged_city_id' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'contact_phone_number' => ['required', 'string'],
            'contact_name' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $countryUUID = $this->input('country_id');
        $stateUUID = $this->input('state_id');
        $cityUUID = $this->input('city_id');

        if (empty($countryUUID) || empty($stateUUID) || empty($cityUUID)) {
            return;
        }

        $country = Country::where('uuid', $countryUUID)->first();

        $state = State::where('uuid', $stateUUID)
            ->where('country_id', $country?->id)
            ->first();

        $city = City::where('uuid', $cityUUID)
            ->where('state_id', $state?->id)
            ->first();

        if (! $country) {
            return;
        }

        if (! $state) {
            throw new HttpResponseException(ApiResponse::error(
                'State does not exist for the selected country.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ));
        }

        if (! $city) {
            throw new HttpResponseException(ApiResponse::error(
                'City does not exist for the selected state.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ));
        }

        $this->merge([
            'merged_country_id' => $country->id,
            'merged_state_id' => $state->id,
            'merged_city_id' => $city->id,
        ]);
    }
}
