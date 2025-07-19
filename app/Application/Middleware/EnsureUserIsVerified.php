<?php

namespace App\Application\Middleware;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Auth\Enums\UserStatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::error('Invalid user', Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->email_verified_at) {
            return ApiResponse::error('User not yet verified', Response::HTTP_UNAUTHORIZED);
        }

        if ($user->status !== UserStatusEnum::ACTIVE->value) {
            return ApiResponse::error('User not active', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
