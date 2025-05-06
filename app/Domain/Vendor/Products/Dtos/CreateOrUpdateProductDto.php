<?php

namespace App\Domain\Vendor\Products\Dtos;

use App\Application\Shared\Enum\ProductEnum;
use App\Application\Shared\Traits\UtilitiesTrait;

class CreateOrUpdateProductDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $categoryUUID,
        private readonly int $vendorId,
        private readonly int $categoryId,
        private readonly ?int $productId,
        private readonly string $name,
        private readonly string $description,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            categoryUUID: $payload['category_id'],
            vendorId: $payload['vendor_id'],
            categoryId: $payload['merged_category_id'],
            productId: $payload['merged_product_id'] ?? null,
            name: $payload['name'],
            description: $payload['description'],
        );
    }

    public function toArray(): array
    {
        if ($this->productId) {
            return [
                'category_id' => $this->categoryId,
                'name' => $this->name,
                'description' => $this->description,
            ];
        }

        return [
            'vendor_id' => $this->vendorId,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description,
            'status' => ProductEnum::ACTIVE->value,
        ];
    }

    public function getVendorId(): int
    {
        return $this->vendorId;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategoryUUID(): string
    {
        return $this->categoryUUID;
    }
}
