<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class PaymentAuthorizationDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $reference,
        private readonly array $authorization,
        private ?string $gatewayReference = null,
        private ?string $authModel = null,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            reference: $payload['reference'],
            authorization: $payload['authorization'],
            authModel: $payload['auth_model'] ?? null,
        );
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getGatewayReference(): ?string
    {
        return $this->gatewayReference;
    }

    public function getAuthModel(): ?string
    {
        return $this->authModel;
    }

    public function getAuthorization(): ?array
    {
        return $this->authorization;
    }

    public function setGatewayReference(string $gatewayReference): void
    {
        $this->gatewayReference = $gatewayReference;
    }

    public function setAuthModel(string $authModel): void
    {
        $this->authModel = $authModel;
    }
}
