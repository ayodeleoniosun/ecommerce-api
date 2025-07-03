<?php

namespace App\Infrastructure\Providers;

use App\Domain\Payment\Interfaces\ApiLogsTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayConfigurationRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Infrastructure\Models\Payment\GatewayConfiguration;
use App\Infrastructure\Repositories\Payment\GatewayRepository;
use App\Infrastructure\Repositories\Payment\GatewayTypeRepository;
use App\Infrastructure\Repositories\Payment\Integrations\Korapay\KorapayApiLogsCardTransactionRepository;
use App\Infrastructure\Repositories\Payment\Integrations\Korapay\KorapayCardTransactionRepository;
use App\Infrastructure\Services\Payments\Korapay\KorapayIntegration;
use App\Infrastructure\Services\Payments\Korapay\Service;
use App\Infrastructure\Services\Payments\PaymentGatewayStrategy;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
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
        $this->app->bind(CardTransactionRepositoryInterface::class, KorapayCardTransactionRepository::class);
        $this->app->bind(ApiLogsTransactionRepositoryInterface::class, KorapayApiLogsCardTransactionRepository::class);

        $this->app->singleton(PaymentGatewayStrategy::class, function (Application $app) {
            return new PaymentGatewayStrategy($this->getGatewayIntegration($app));
        });
    }

    /**
     * @throws BindingResolutionException
     */
    private function getGatewayIntegration(Application $app): array
    {
        return [
            'korapay' => new KorapayIntegration($app->make(Service::class)),
        ];
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
