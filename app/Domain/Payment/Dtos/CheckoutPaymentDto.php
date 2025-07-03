<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class CheckoutPaymentDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly ?array $card,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            card: $payload['card']
        );
    }

    public function getCardData(): ?array
    {
        return $this->card;
    }
}
