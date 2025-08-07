<?php

namespace App\Domain\Payment\Requests;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class FundWalletRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:100', 'max:1000000', 'digits_between:1,7'],
            'currency' => ['required', new Enum(CurrencyEnum::class)],
            'card' => ['required', 'array'],
            'card.name' => ['required', 'string'],
            'card.number' => ['required', 'string'],
            'card.cvv' => ['required', 'string'],
            'card.expiry_month' => ['required', 'string'],
            'card.expiry_year' => ['required', 'string'],
            'card.pin' => ['required', 'string'],
        ];
    }
}
