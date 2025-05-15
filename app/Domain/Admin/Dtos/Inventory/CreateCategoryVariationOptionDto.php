<?php

namespace App\Domain\Admin\Dtos\Inventory;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Admin\Requests\Category\CategoryVariationOptionRequest;

class CreateCategoryVariationOptionDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $categoryVariationUUID,
        private readonly array $values,
        private ?string $categoryVariationId = null,
    ) {}

    public static function fromRequest(CategoryVariationOptionRequest $request): self
    {
        return new self(
            categoryVariationUUID: $request->input('category_variation_id'),
            values: $request->input('values'),
        );
    }

    public function toArray(): array
    {
        $values = [];

        foreach ($this->values as $value) {
            $values[] = [
                'variation_id' => $this->categoryVariationId,
                'uuid' => self::generateUUID(),
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $values;
    }

    public function getCategoryVariationUUID(): string
    {
        return $this->categoryVariationUUID;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setCategoryVariationId(string $categoryVariationId): void
    {
        $this->categoryVariationId = $categoryVariationId;
    }
}
