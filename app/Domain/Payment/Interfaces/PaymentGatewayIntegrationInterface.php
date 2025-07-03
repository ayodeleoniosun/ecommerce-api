<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;

interface PaymentGatewayIntegrationInterface
{
    public function initialize(InitiateOrderPaymentDto $paymentDto): array;
}
