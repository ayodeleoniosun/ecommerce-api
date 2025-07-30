<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Payment\Dtos\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;

interface PaymentGatewayIntegrationInterface
{
    public function initiate(InitiateCardPaymentDto $paymentDto): PaymentResponseDto;

    public function authorize(PaymentAuthorizationDto $paymentAuthorizationDto): PaymentResponseDto;

    public function verify(string $reference);
}
