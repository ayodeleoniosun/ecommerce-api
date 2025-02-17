<?php

namespace App\Providers;

use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Infrastructure\Repositories\User\UserRepository;
use App\Infrastructure\Repositories\User\UserVerificationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserVerificationRepositoryInterface::class, UserVerificationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
