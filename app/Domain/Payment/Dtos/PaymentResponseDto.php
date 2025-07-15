<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class PaymentResponseDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $status,
        private readonly string $authModel,
        private readonly string $gateway,
        private readonly string $reference,
        private readonly string $responseMessage,
        private readonly ?string $errorType = null,
        private ?string $redirectionUrl = null,
        private readonly ?int $amountCharged = null,
        private readonly ?int $fee = null,
        private readonly ?int $vat = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'amount' => $this->amountCharged,
            'fee' => $this->fee,
            'vat' => $this->vat,
            'redirection_url' => $this->redirectionUrl,
            'status' => $this->status,
            'auth_model' => $this->authModel,
            'reference' => $this->reference,
            'response_message' => $this->responseMessage,
        ], function ($value) {
            return $value !== null;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getErrorType(): ?string
    {
        return $this->errorType;
    }

    public function getFee(): ?int
    {
        return $this->fee;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function getAmountCharged(): ?int
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

    public function setRedirectionUrl(string $url): void
    {
        $this->redirectionUrl = $url;
    }
}
