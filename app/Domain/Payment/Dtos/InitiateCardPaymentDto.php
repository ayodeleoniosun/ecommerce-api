<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class InitiateCardPaymentDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly int $amount,
        private readonly string $currency,
        private readonly CardData $card,
        private readonly CustomerData $customer,
        private readonly string $redirectUrl,
        private ?string $orderPaymentReference = null,
        private ?string $gatewayReference = null,
    ) {}

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'reference' => $this->gatewayReference,
            'currency' => $this->currency,
            'card' => CardData::toArray($this->card),
            'customer' => CustomerData::toArray($this->customer),
            'redirect_url' => $this->redirectUrl,
        ];
    }

    public function toTransactionArray(): array
    {
        return [
            'amount' => $this->amount,
            'reference' => $this->gatewayReference,
            'currency' => $this->currency,
            'order_payment_reference' => $this->orderPaymentReference,
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

    public function getCard(): CardData
    {
        return $this->card;
    }

    public function getCustomer(): CustomerData
    {
        return $this->customer;
    }

    public function setOrderPaymentReference(string $reference): void
    {
        $this->orderPaymentReference = $reference;
    }

    public function getOrderPaymentReference(): ?string
    {
        return $this->orderPaymentReference;
    }

    public function setGatewayReference(string $reference): void
    {
        $this->gatewayReference = $reference;
    }

    public function getGatewayReference(): ?string
    {
        return $this->gatewayReference;
    }
}
