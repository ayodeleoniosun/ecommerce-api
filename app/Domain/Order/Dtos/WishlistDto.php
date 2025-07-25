<?php

namespace App\Domain\Order\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class WishlistDto
{
    use UtilitiesTrait;

    public function __construct(
        private string $productItemUUID,
        private readonly int $productItemId,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            productItemUUID: $payload['product_item_id'],
            productItemId: $payload['merged_product_item_id']
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'user_id' => auth()->user()->id,
            'product_item_id' => $this->getProductItemId(),
        ];
    }

    public function getProductItemId(): int
    {
        return $this->productItemId;
    }

    public function getProductItemUUID(): string
    {
        return $this->productItemUUID;
    }

    public function setProductItemUUID(string $uuid): void
    {
        $this->productItemUUID = $uuid;
    }
}
