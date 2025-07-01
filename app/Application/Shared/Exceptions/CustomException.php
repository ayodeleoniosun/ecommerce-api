<?php

namespace App\Application\Shared\Exceptions;

use App\Application\Shared\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CustomException extends Exception
{
    public function __construct(
        public $message,
        public int $statusCode = ResponseAlias::HTTP_BAD_REQUEST,
        public array $errors = [],
    ) {}

    public function report(): bool
    {
        return true;
    }

    public function render(): JsonResponse
    {
        return ApiResponse::error($this->message, $this->statusCode);
    }
}
