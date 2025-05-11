<?php

namespace App\Domain\Vendor\Products\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class CreateOrUpdateProductDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $categoryUUID,
        private readonly int $vendorId,
        private readonly int $categoryId,
        private readonly string $name,
        private readonly string $description,
        private readonly ?int $productId = null,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            categoryUUID: $payload['category_id'],
            vendorId: $payload['vendor_id'],
            categoryId: $payload['merged_category_id'],
            name: $payload['name'],
            description: $payload['description'],
            productId: $payload['merged_product_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        if ($this->productId) {
            return [
                'category_id' => $this->categoryId,
                'name' => strtolower($this->name),
                'description' => $this->description,
            ];
        }

        return [
            'vendor_id' => $this->vendorId,
            'category_id' => $this->categoryId,
            'name' => strtolower($this->name),
            'description' => $this->description,
        ];
    }

    public function getVendorId(): int
    {
        return $this->vendorId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return strtolower($this->name);
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
