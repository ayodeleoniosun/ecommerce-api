<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class InitiateOrderPaymentDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly int $amount,
        private readonly string $currency,
        private readonly CardData $card,
        private readonly CustomerData $customer,
        private readonly string $redirectUrl,
        private ?string $reference = null,
        private ?int $paymentId = null,
    ) {}

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'reference' => $this->reference,
            'currency' => $this->currency,
            'card' => CardData::toArray($this->card),
            'customer' => CustomerData::toArray($this->customer),
            'redirect_url' => $this->redirectUrl,
        ];
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setPaymentId(int $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }
}
