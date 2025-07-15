<?php

namespace App\Domain\Payment\Requests;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentAuthorizationRequest extends FormRequest
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
            'reference' => ['required', 'string'],
            'authorization' => ['required', 'array'],
            'authorization.otp' => ['required_if:auth_model,OTP', 'string'],
            'authorization.avs' => ['required_if:auth_model,AVS', 'array'],
            'authorization.avs.state' => ['required_if:auth_model,AVS', 'string'],
            'authorization.avs.city' => ['required_if:auth_model,AVS', 'string'],
            'authorization.avs.country' => ['required_if:auth_model,AVS', 'string'],
            'authorization.avs.address' => ['required_if:auth_model,AVS', 'string'],
            'authorization.avs.zip_code' => ['required_if:auth_model,AVS', 'string'],
        ];
    }
}
