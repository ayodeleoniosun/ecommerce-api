<?php

namespace App\Domain\Payment\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class CheckoutPaymentDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $orderID,
        private readonly int $mergedOrderId,
        private readonly array $card,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            orderID: $payload['order_id'],
            mergedOrderId: $payload['merged_order_id'],
            card: $payload['card']
        );
    }

    public function getOrderId(): int
    {
        return $this->mergedOrderId;
    }

    public function getCardData(): array
    {
        return $this->card;
    }
}
