<?php

namespace App\Application\Shared\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiResponse
{
    public static function success(
        string $message = 'Success',
        $data = [],
        int $code = Response::HTTP_OK,
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function error(
        string $message,
        int $code,
        $errors = [],
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        $statusCode = array_key_exists($code, Response::$statusTexts) ? $code : Response::HTTP_BAD_REQUEST;

        return response()->json($response, $statusCode);
    }
}
