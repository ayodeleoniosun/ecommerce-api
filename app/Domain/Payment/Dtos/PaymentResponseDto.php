<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class PaymentResponseDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly int $amountCharged,
        private readonly int $fee,
        private readonly int $vat,
        private readonly string $status,
        private readonly string $authModel,
        private readonly string $gateway,
        private readonly string $reference,
        private readonly string $responseMessage,
    ) {}

    public function getFee(): int
    {
        return $this->fee;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function getAmountCharged(): int
    {
        return $this->amountCharged;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAuthModel(): string
    {
        return $this->authModel;
    }

    public function getGateway(): string
    {
        return $this->gateway;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }
}
