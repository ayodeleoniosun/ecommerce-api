<?php

namespace App\Domain\Order\Requests;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateDeliveryAddressRequest extends FormRequest
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
            'address' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],
            'additional_note' => ['sometimes', 'string'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return array_merge(parent::validated($key, $default), [
            'user_id' => auth()->user()?->id,
        ]);
    }
}
