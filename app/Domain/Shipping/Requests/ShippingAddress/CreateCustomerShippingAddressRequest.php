<?php

namespace App\Domain\Shipping\Requests\ShippingAddress;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Domain\Shipping\Enums\AddressTypeEnum;
use App\Infrastructure\Models\Shipping\Address\City;
use App\Infrastructure\Models\Shipping\Address\Country;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;
use App\Infrastructure\Models\Shipping\Address\State;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CreateCustomerShippingAddressRequest extends FormRequest
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
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'country_id' => ['required', 'string', 'exists:countries,uuid'],
            'state_id' => ['required', 'string', 'exists:states,uuid'],
            'city_id' => ['required', 'string', 'exists:cities,uuid'],
            'merged_country_id' => ['required', 'integer'],
            'merged_state_id' => ['required', 'integer'],
            'merged_city_id' => ['required', 'integer'],
            'address' => ['required', 'string'],
            'additional_note' => ['sometimes', 'string'],
            'default' => ['sometimes', 'boolean'],
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
                Response::HTTP_NOT_FOUND,
            ));
        }

        if (! $city) {
            throw new HttpResponseException(ApiResponse::error(
                'City does not exist for the selected state.',
                Response::HTTP_NOT_FOUND,
            ));
        }

        if ($this->input('default') === true) {
            $defaultAddress = CustomerShippingAddress::where('user_id', auth()->user()->id)
                ->where('status', AddressTypeEnum::DEFAULT->value)
                ->first();

            if ($defaultAddress) {
                throw new HttpResponseException(ApiResponse::error(
                    'A default shipping address already exist',
                    Response::HTTP_CONFLICT,
                ));
            }
        }

        $this->merge([
            'merged_country_id' => $country->id,
            'merged_state_id' => $state->id,
            'merged_city_id' => $city->id,
        ]);
    }
}
