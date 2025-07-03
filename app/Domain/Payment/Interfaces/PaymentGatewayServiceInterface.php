<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;

interface PaymentGatewayServiceInterface
{
    public function initiate(InitiateOrderPaymentDto $paymentDto): array;

    public function verify(string $reference): array;
}
