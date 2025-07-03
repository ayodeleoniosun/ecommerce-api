<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Interfaces\PaymentGatewayIntegrationInterface;
use Illuminate\Http\Client\ConnectionException;

class KorapayIntegration implements PaymentGatewayIntegrationInterface
{
    public function __construct(
        public readonly Service $service,
    ) {}

    /**
     * @throws ConnectionException
     */
    public function initialize(InitiateOrderPaymentDto $paymentDto): array
    {
        return $this->service->initiate($paymentDto);
    }
}
