<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class PaymentDto extends InitiateCardPaymentDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $paymentMethod,
        private readonly ?array $card,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            paymentMethod: $payload['payment_method'],
            card: $payload['card'] ?? null
        );
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getCardData(): ?array
    {
        return $this->card;
    }
}
