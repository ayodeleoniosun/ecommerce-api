<?php

namespace App\Domain\Payment\Dtos;

class GatewayFilterData
{
    public function __construct(
        private readonly string $type,
        private readonly string $category,
        private readonly string $currency,
        private readonly ?int $gatewayId = null,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getGatewayId(): ?int
    {
        return $this->gatewayId;
    }
}
