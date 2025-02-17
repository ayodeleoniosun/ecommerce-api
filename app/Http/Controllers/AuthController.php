<?php

namespace App\Http\Controllers;

use App\Application\Actions\RegisterUser;
use App\Application\Actions\ResendToken;
use App\Application\Actions\VerifyToken;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\User\Entities\User\User;
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
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = (object) $request->validated();
        $user = new User($data->firstname, $data->lastname, $data->email, $data->password);
        $data = $this->registerUser->execute($user);

        return ApiResponse::success('User registered successfully', $data, Response::HTTP_CREATED);
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
