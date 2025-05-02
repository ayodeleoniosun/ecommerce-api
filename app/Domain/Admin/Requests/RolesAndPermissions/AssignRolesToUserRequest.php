<?php

namespace App\Domain\Admin\Requests\RolesAndPermissions;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class AssignRolesToUserRequest extends FormRequest
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
            'role_user_id' => ['required', 'string', 'exists:users,uuid'],
            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'exists:roles,name'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->user()->id,
        ]);
    }

    /**
     * @throws HttpResponseException|HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $formattedErrors = [];

        foreach ($errors as $key => $error) {
            if (str_contains($key, 'roles.')) {
                $index = str_replace('roles.', '', $key);
                $roleName = $this->input('roles')[$index] ?? 'Unknown Role';
                $formattedErrors[] = "The selected role '{$roleName}' is invalid.";
            }
        }

        throw new HttpResponseException(
            ApiResponse::error($formattedErrors[0], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
