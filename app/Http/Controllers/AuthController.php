<?php

namespace App\Http\Controllers;

use App\Application\Actions\RegisterUser;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\User\Entities\User\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController
{
    public function __construct(private readonly RegisterUser $registerUser)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = (object) $request->validated();
        $user = new User($data->firstname, $data->lastname, $data->email, $data->password);
        $data = $this->registerUser->execute($user);

        return ApiResponse::success('User registered successfully', $data, Response::HTTP_CREATED);
    }
}
