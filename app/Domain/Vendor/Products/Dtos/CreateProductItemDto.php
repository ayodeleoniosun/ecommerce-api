<?php

namespace App\Domain\Vendor\Products\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class CreateProductItemDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $productUUID,
        private readonly string $productId,
        private readonly string $categoryVariationOptionUUID,
        private readonly string $categoryVariationOptionId,
        private readonly int $price,
        private readonly int $quantity,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            productUUID: $payload['product_id'],
            productId: $payload['merged_product_id'],
            categoryVariationOptionUUID: $payload['variation_option_id'],
            categoryVariationOptionId: $payload['merged_variation_option_id'],
            price: $payload['price'],
            quantity: $payload['quantity'],
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'variation_option_id' => $this->categoryVariationOptionId,
            'price' => $this->price,
            'quantity' => $this->quantity,
        ];
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getCategoryVariationOptionId(): string
    {
        return $this->categoryVariationOptionId;
    }
}
