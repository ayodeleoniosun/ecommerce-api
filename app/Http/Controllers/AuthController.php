<?php

namespace App\Http\Controllers;

use App\Application\Actions\RegisterUser;
use App\Application\Actions\VerifyAccount;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\User\Entities\User\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyAccountRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController
{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly VerifyAccount $verifyAccount,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = (object) $request->validated();
        $user = new User($data->firstname, $data->lastname, $data->email, $data->password);
        $data = $this->registerUser->execute($user);

        return ApiResponse::success('User registered successfully', $data, Response::HTTP_CREATED);
    }

    public function verifyAccount(VerifyAccountRequest $request): JsonResponse
    {
        try {
            $data = $this->verifyAccount->execute($request->validated()['token']);

            return ApiResponse::success('User verified successfully', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
