<?php

namespace App\Domain\Shipping\Requests\PickupStation;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreatePickupStationOpeningHourRequest extends FormRequest
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
            'pickup_station_id' => ['required', 'string', 'exists:pickup_stations,uuid'],
            'merged_pickup_station_id' => ['required', 'integer'],
            'day_of_week' => ['required', 'string'],
            'opens_at' => ['required', 'string'],
            'closes_at' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $pickupStationUUID = $this->input('pickup_station_id');

        if (empty($pickupStationUUID)) {
            return;
        }

        $pickupStation = PickupStation::where('uuid', $pickupStationUUID)->first();

        $this->merge([
            'merged_pickup_station_id' => $pickupStation?->id,
        ]);
    }
}
