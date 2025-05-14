<?php

namespace App\Domain\Admin\Requests\RolesAndPermissions;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\User\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class RevokeRoleFromUserRequest extends FormRequest
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
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $userUUID = $this->input('user_id');

        if (empty($userUUID)) {
            return;
        }

        $user = User::where('uuid', $userUUID)->first();

        if (! $user) {
            throw new HttpResponseException(ApiResponse::error('User does not exist', Response::HTTP_NOT_FOUND));
        }
    }
}
