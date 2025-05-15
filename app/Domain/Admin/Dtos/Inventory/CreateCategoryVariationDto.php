<?php

namespace App\Domain\Admin\Dtos\Inventory;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Admin\Requests\Category\CategoryVariationRequest;

class CreateCategoryVariationDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $categoryUUID,
        private readonly array $variations,
        private ?string $categoryId = null,
    ) {}

    public static function fromRequest(CategoryVariationRequest $request): self
    {
        return new self(
            categoryUUID: $request->input('category_id'),
            variations: $request->input('variations'),
        );
    }

    public function toArray(): array
    {
        $variations = [];

        foreach ($this->variations as $variation) {
            $variations[] = [
                'category_id' => $this->categoryId,
                'uuid' => self::generateUUID(),
                'name' => $variation,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $variations;
    }

    public function getCategoryUUID(): string
    {
        return $this->categoryUUID;
    }

    public function getVariations(): array
    {
        return $this->variations;
    }

    public function setCategoryId(string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }
}
