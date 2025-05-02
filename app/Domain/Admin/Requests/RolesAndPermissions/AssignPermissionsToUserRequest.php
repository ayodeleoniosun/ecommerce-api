<?php

namespace App\Domain\Admin\Requests\RolesAndPermissions;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class AssignPermissionsToUserRequest extends FormRequest
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
            'permission_user_id' => ['required', 'string', 'exists:users,uuid'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'exists:permissions,name'],
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
            if (str_contains($key, 'permissions.')) {
                $index = str_replace('permissions.', '', $key);
                $permissionName = $this->input('permissions')[$index] ?? 'Unknown Role';
                $formattedErrors[] = "The selected permission '{$permissionName}' is invalid.";
            }
        }

        throw new HttpResponseException(
            ApiResponse::error($formattedErrors[0], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
