<?php

namespace App\Application\Http\Auth\Controllers;

use App\Application\Actions\Auth\InitiateForgotPassword;
use App\Application\Actions\Auth\LoginUser;
use App\Application\Actions\Auth\RegisterUser;
use App\Application\Actions\Auth\ResendToken;
use App\Application\Actions\Auth\ResetPassword;
use App\Application\Actions\Auth\VerifyToken;
use App\Application\Http\Auth\Requests\ForgotPasswordRequest;
use App\Application\Http\Auth\Requests\LoginRequest;
use App\Application\Http\Auth\Requests\RegisterRequest;
use App\Application\Http\Auth\Requests\ResendTokenRequest;
use App\Application\Http\Auth\Requests\ResetPasswordRequest;
use App\Application\Http\Auth\Requests\VerifyTokenRequest;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Auth\Entities\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

class AuthController
{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly VerifyToken $verifyToken,
        private readonly ResendToken $resendToken,
        private readonly LoginUser $loginUser,
        private readonly InitiateForgotPassword $forgotPassword,
        private readonly ResetPassword $resetPassword,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = (object) $request->validated();
        $user = new User($data->firstname, $data->lastname, $data->email, $data->password, $data->type);
        $data = $this->registerUser->execute($user);

        return ApiResponse::success('User registered successfully', $data, Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->loginUser->execute($request->validated());

            return ApiResponse::success('User logged in successfully', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function verifyToken(VerifyTokenRequest $request): JsonResponse
    {
        try {
            $data = $this->verifyToken->execute($request->validated()['token']);

            return ApiResponse::success('User verified successfully', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function resendToken(ResendTokenRequest $request): JsonResponse
    {
        try {
            $this->resendToken->execute($request->validated()['email']);

            return ApiResponse::success('Verification link resent successfully');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $status = $this->forgotPassword->execute($request->validated()['email']);

            if ($status === Password::RESET_LINK_SENT) {
                return ApiResponse::success('Forgot password link resent successfully');
            }

            if ($status === Password::RESET_THROTTLED) {
                return ApiResponse::error('You cannot send more than 1 password request per minute. Try again later.',
                    Response::HTTP_TOO_MANY_REQUESTS);
            }

            return ApiResponse::error('Forgot password not successful. Please try again.');

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $status = $this->resetPassword->execute($request->validated());

            if ($status === Password::PASSWORD_RESET) {
                return ApiResponse::success('Password successfully reset. You can login now');
            }

            if ($status === Password::INVALID_TOKEN) {
                return ApiResponse::error('Invalid token');
            }

            return ApiResponse::error('Password reset not successful. Please try again.');

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
