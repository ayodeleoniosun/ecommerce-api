<?php

use App\Application\Middleware\EnsureUserIsVerified;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Order\Commands\RestoreAbandonedCartQuantity;
use App\Infrastructure\Providers\InventoryServiceProvider;
use App\Infrastructure\Providers\OnboardingServiceProvider;
use App\Infrastructure\Providers\OrderServiceProvider;
use App\Infrastructure\Providers\PaymentGatewayServiceProvider;
use App\Infrastructure\Providers\ShippingServiceProvider;
use App\Infrastructure\Providers\UserServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'verified' => EnsureUserIsVerified::class,
        ]);
    })
    ->withEvents(discover: [
        __DIR__.'/../app/Domain/*/Events',
        __DIR__.'/../app/Domain/*/Listeners',
    ])
    ->withCommands([
        RestoreAbandonedCartQuantity::class,
    ])
    ->withProviders(([
        UserServiceProvider::class,
        OnboardingServiceProvider::class,
        InventoryServiceProvider::class,
        OrderServiceProvider::class,
        ShippingServiceProvider::class,
        PaymentGatewayServiceProvider::class,
    ]))
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (ResourceNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), $e->getStatusCode());
        });

        $exceptions->report(function (BadRequestException $e) {
            return ApiResponse::error($e->getMessage(), $e->getStatusCode());
        });
    })->create();
