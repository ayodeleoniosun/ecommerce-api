<?php

namespace App\Domain\Payment\Dtos\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;

class FundWalletDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly int $amount,
        private readonly string $currency,
        private readonly array $card,
        private ?string $reference = null,
        private ?string $gateway = null,
        private ?string $gatewayReference = null,
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getGateway(): ?string
    {
        return $this->gateway;
    }

    public function getGatewayReference(): ?string
    {
        return $this->gatewayReference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function setGatewayReference(string $gatewayReference): void
    {
        $this->gatewayReference = $gatewayReference;
    }

    public function setGateway(string $gateway): void
    {
        $this->gateway = $gateway;
    }
}
