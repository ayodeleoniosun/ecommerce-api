<?php

namespace App\Http\Requests;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:users'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed'],
            'password_confirmation' => [
                'required', Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password_confirmation.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
