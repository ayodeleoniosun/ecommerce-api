<?php

namespace App\Domain\Order\Requests;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Shipping\Enums\DeliveryTypeEnum;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;

class CheckoutRequest extends FormRequest
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
            'customer_address_id' => ['required', 'string', 'exists:customer_shipping_addresses,uuid'],
            'pickup_station_id' => ['sometimes', 'string', 'exists:pickup_stations,uuid'],
            'delivery_type' => ['required', new Enum(DeliveryTypeEnum::class)],
            'payment_method' => ['required', new Enum(PaymentTypeEnum::class)],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return array_merge(parent::validated($key, $default), [
            'user_id' => auth()->user()?->id,
        ]);
    }

    protected function prepareForValidation(): void
    {
        $customerAddressUUID = $this->input('customer_address_id');
        $pickupStationUUID = $this->input('pickup_station_id');

        if (empty($customerAddressUUID) || empty($pickupStationUUID)) {
            return;
        }

        $customerAddress = CustomerShippingAddress::where('uuid', $customerAddressUUID)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (! $customerAddress) {
            throw new HttpResponseException(
                ApiResponse::error('Invalid customer address', Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
