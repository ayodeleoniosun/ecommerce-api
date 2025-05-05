<?php

namespace App\Domain\Vendor\Products\Dtos;

use App\Application\Shared\Enum\ProductEnum;
use App\Application\Shared\Traits\UtilitiesTrait;

class CreateProductDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly int $vendorId,
        private readonly string $categoryUUID,
        private readonly string $categoryId,
        private readonly string $name,
        private readonly string $description,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            vendorId: $payload['vendor_id'],
            categoryUUID: $payload['category_id'],
            categoryId: $payload['merged_category_id'],
            name: $payload['name'],
            description: $payload['description'],
        );
    }

    public function toArray(): array
    {
        return [
            'vendor_id' => $this->vendorId,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description,
            'status' => ProductEnum::ACTIVE->value,
        ];
    }

    public function getVendorId(): string
    {
        return $this->vendorId;
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
