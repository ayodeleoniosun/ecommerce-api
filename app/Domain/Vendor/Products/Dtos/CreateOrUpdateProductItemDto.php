<?php

namespace App\Domain\Vendor\Products\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class CreateOrUpdateProductItemDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $productUUID,
        private readonly string $categoryVariationOptionUUID,
        private readonly int $productId,
        private readonly int $categoryVariationOptionId,
        private readonly int $price,
        private readonly string $currency,
        private readonly int $quantity,
        private readonly ?int $productItemId = null,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            productUUID: $payload['product_id'],
            categoryVariationOptionUUID: $payload['variation_option_id'],
            productId: $payload['merged_product_id'],
            categoryVariationOptionId: $payload['merged_variation_option_id'],
            price: $payload['price'],
            currency: $payload['currency'],
            quantity: $payload['quantity'],
            productItemId: $payload['merged_product_item_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'variation_option_id' => $this->categoryVariationOptionId,
            'currency' => $this->currency,
            'price' => $this->price,
            'quantity' => $this->quantity,
        ];
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductItemId(): ?int
    {
        return $this->productItemId;
    }

    public function getCategoryVariationOptionId(): int
    {
        return $this->categoryVariationOptionId;
    }
}
