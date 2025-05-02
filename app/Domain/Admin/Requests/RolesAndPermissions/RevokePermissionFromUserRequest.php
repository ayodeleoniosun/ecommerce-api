<?php

namespace App\Domain\Admin\Requests\RolesAndPermissions;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RevokePermissionFromUserRequest extends FormRequest
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
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->user()->id,
        ]);
    }
}
