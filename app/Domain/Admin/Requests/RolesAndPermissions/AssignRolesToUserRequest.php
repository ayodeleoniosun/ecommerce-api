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
            'user_id' => ['required', 'string', 'exists:users,uuid'],
            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'exists:roles,name'],
        ];
    }

    /**
     * @throws HttpResponseException|HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $invalidRoles = [];

        foreach ($errors as $key => $error) {
            if (str_contains($key, 'roles.')) {
                $index = str_replace('roles.', '', $key);
                $invalidRoles[] = $this->input('roles')[$index] ?? 'Unknown Role';
            }
        }

        if (! empty($invalidRoles)) {
            $roles = implode(', ', $invalidRoles);

            throw new HttpResponseException(
                ApiResponse::error("The selected roles: {$roles} are invalid.",
                    Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
