<?php

namespace App\Domain\Payment\Requests;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OrderPaymentRequest extends FormRequest
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
            'payment_method' => ['required', new Enum(PaymentTypeEnum::class)],
            'card' => ['sometimes', 'array'],
            'card.name' => ['sometimes', 'string'],
            'card.number' => ['sometimes', 'string'],
            'card.cvv' => ['sometimes', 'string'],
            'card.expiry_month' => ['sometimes', 'string'],
            'card.expiry_year' => ['sometimes', 'string'],
            'card.pin' => ['sometimes', 'string'],
        ];
    }
}
