<?php

namespace App\Http\Controllers;

use App\Application\Actions\Auth\LoginUser;
use App\Application\Actions\Auth\RegisterUser;
use App\Application\Actions\Auth\ResendToken;
use App\Application\Actions\Auth\VerifyToken;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Auth\Entities\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendTokenRequest;
use App\Http\Requests\VerifyTokenRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController
{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly VerifyToken $verifyToken,
        private readonly ResendToken $resendToken,
        private readonly LoginUser $loginUser,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = (object) $request->validated();
        $user = new User($data->firstname, $data->lastname, $data->email, $data->password);
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
}
