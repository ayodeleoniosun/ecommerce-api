<?php

namespace App\Domain\Payment\Requests;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Domain\Payment\Dtos\CardData;
use App\Infrastructure\Models\Order\Order;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CheckoutPaymentRequest extends FormRequest
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
            'order_id' => ['required', 'string', 'exists:orders,uuid'],
            'merged_order_id' => ['required', 'integer', 'exists:orders,id'],
            'card' => ['sometimes'],
        ];
    }

    public function cardData(): CardData
    {
        $cardArray = $this->validated()['card'];

        return CardData::fromArray($cardArray);
    }

    protected function prepareForValidation(): void
    {
        $orderUUID = $this->input('order_id');

        if (empty($orderUUID)) {
            return;
        }

        $order = Order::query()
            ->where('uuid', $orderUUID)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (! $order) {
            throw new HttpResponseException(
                ApiResponse::error('Invalid order request', Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        $this->merge([
            'merged_order_id' => $order?->id,
        ]);
    }
}
