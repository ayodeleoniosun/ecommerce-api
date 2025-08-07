<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class PaymentResponseDto
{
    use UtilitiesTrait;

    public function __construct(
        private string $status,
        private readonly string $paymentMethod,
        private string $reference,
        private readonly string $responseMessage,
        private ?string $authModel = null,
        private readonly ?string $gateway = null,
        private ?string $redirectionUrl = null,
        private ?float $amountCharged = null,
        private ?float $fee = 0,
        private ?float $vat = 0,
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
            'payment_method' => $this->paymentMethod,
        ], function ($value) {
            return $value !== null;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getRedirectionUrl(): ?string
    {
        return $this->redirectionUrl;
    }

    public function getFee(): ?float
    {
        return $this->fee;
    }

    public function getVat(): ?float
    {
        return $this->vat;
    }

    public function getAmountCharged(): ?float
    {
        return $this->amountCharged;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAuthModel(): ?string
    {
        return $this->authModel;
    }

    public function getGateway(): ?string
    {
        return $this->gateway;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setRedirectionUrl(string $url): void
    {
        $this->redirectionUrl = $url;
    }

    public function setAuthModel(string $authModel): void
    {
        $this->authModel = $authModel;
    }

    public function setAmountCharged(float $amountCharged): void
    {
        $this->amountCharged = $amountCharged;
    }

    public function setFee(float $fee): void
    {
        $this->fee = $fee;
    }

    public function setVat(float $vat): void
    {
        $this->vat = $vat;
    }
}
