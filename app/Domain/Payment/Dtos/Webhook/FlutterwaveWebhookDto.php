<?php

namespace App\Domain\Payment\Dtos\Webhook;

use App\Application\Shared\Traits\UtilitiesTrait;

class FlutterwaveWebhookDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $id,
        private readonly string $transactionReference,
        private readonly string $flutterwaveReference,
        private readonly string $amount,
        private readonly string $chargedAmount,
        private readonly string $status,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            id: $payload['id'],
            transactionReference: $payload['txRef'],
            flutterwaveReference: $payload['flwRef'],
            amount: $payload['amount'],
            chargedAmount: $payload['charged_amount'],
            status: $payload['status']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'txRef' => $this->transactionReference,
            'flwRef' => $this->flutterwaveReference,
            'amount' => $this->amount,
            'charged_amount' => $this->chargedAmount,
            'status' => $this->status,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTransactionReference(): string
    {
        return $this->transactionReference;
    }

    public function getFlutterwaveReference(): string
    {
        return $this->flutterwaveReference;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getChargedAmount(): string
    {
        return $this->chargedAmount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
