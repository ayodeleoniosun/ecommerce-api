<?php

namespace App\Infrastructure\Providers;

use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayConfigurationRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Domain\Payment\Interfaces\PaymentGatewayIntegrationInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletAuditLogRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletOrderPaymentRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Infrastructure\Models\Payment\GatewayConfiguration;
use App\Infrastructure\Repositories\Payment\CardTransactionRepository;
use App\Infrastructure\Repositories\Payment\GatewayRepository;
use App\Infrastructure\Repositories\Payment\GatewayTypeRepository;
use App\Infrastructure\Repositories\Payment\Wallet\WalletAuditLogRepository;
use App\Infrastructure\Repositories\Payment\Wallet\WalletOrderPaymentRepository;
use App\Infrastructure\Repositories\Payment\Wallet\WalletRepository;
use App\Infrastructure\Repositories\Payment\Wallet\WalletTransactionRepository;
use App\Infrastructure\Services\Payments\PaymentGatewayIntegration;
use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GatewayRepositoryInterface::class, GatewayRepository::class);
        $this->app->bind(GatewayTypeRepositoryInterface::class, GatewayTypeRepository::class);
        $this->app->bind(GatewayConfigurationRepositoryInterface::class, GatewayConfiguration::class);
        $this->app->bind(PaymentGatewayIntegrationInterface::class, PaymentGatewayIntegration::class);
        $this->app->bind(CardTransactionRepositoryInterface::class, CardTransactionRepository::class);

        /* Wallet */
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(WalletTransactionRepositoryInterface::class, WalletTransactionRepository::class);
        $this->app->bind(WalletAuditLogRepositoryInterface::class, WalletAuditLogRepository::class);
        $this->app->bind(WalletOrderPaymentRepositoryInterface::class, WalletOrderPaymentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
