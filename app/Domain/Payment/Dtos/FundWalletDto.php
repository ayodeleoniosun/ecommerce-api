<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class FundWalletDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly int $amount,
        private readonly string $currency,
        private readonly array $card,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            amount: $payload['amount'],
            currency: $payload['currency'],
            card: $payload['card']
        );
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCardData(): array
    {
        return $this->card;
    }
}
