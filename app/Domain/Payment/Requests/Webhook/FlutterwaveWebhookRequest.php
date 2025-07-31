<?php

namespace App\Domain\Payment\Requests\Webhook;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FlutterwaveWebhookRequest extends FormRequest
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
            'id' => ['required', 'integer'],
            'txRef' => ['required', 'string'],
            'amount' => ['required', 'integer'],
            'charged_amount' => ['required', 'integer'],
            'status' => ['required', 'string'],
        ];
    }
}
