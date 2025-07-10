<?php

namespace App\Infrastructure\Services\Payments;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\PaymentGatewayIntegrationInterface;
use App\Infrastructure\Services\Payments\Flutterwave\FlutterwaveIntegration;
use App\Infrastructure\Services\Payments\Korapay\KorapayIntegration;

class PaymentGateway
{
    /**
     * @throws BadRequestException
     */
    public static function make(
        string $gateway,
        CardTransactionRepositoryInterface $cardTransactionRepository,
    ): PaymentGatewayIntegrationInterface {
        return match ($gateway) {
            'korapay' => new KorapayIntegration($cardTransactionRepository),
            'flutterwave' => new FlutterwaveIntegration($cardTransactionRepository),
            default => throw new BadRequestException("Payment gateway {$gateway} is not supported.")
        };
    }
}
