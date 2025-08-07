<?php

namespace App\Domain\Payment\Dtos\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;

class WalletFundingResponseDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $status,
        private readonly float $amount,
        private readonly string $currency,
        private readonly string $paymentMethod,
        private readonly string $reference,
    ) {}

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->paymentMethod,
            'reference' => $this->reference,
        ];
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getReference(): string
    {
        return $this->reference;
    }
}
