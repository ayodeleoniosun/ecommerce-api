<?php

namespace App\Infrastructure\Services\Payments;

use App\Domain\Payment\Interfaces\PaymentGatewayIntegrationInterface;
use InvalidArgumentException;

class PaymentGatewayStrategy
{
    protected array $gateways;

    public function __construct(array $gateways)
    {
        $this->gateways = $gateways;
    }

    public function getGatewayInstance(string $gateway): PaymentGatewayIntegrationInterface
    {
        if (! array_key_exists($gateway, $this->gateways)) {
            throw new InvalidArgumentException("Payment gateway {$gateway} is not supported.");
        }

        return $this->gateways[$gateway];
    }
}
