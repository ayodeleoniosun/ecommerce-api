<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;

interface PaymentGatewayIntegrationInterface
{
    public function initiate(InitiateOrderPaymentDto $paymentDto): PaymentResponseDto;

    public function verify(string $reference): array;
}
