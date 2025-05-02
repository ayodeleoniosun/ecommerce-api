<?php

namespace App\Domain\Admin\Dtos;

use App\Domain\Admin\Requests\Category\CategoryVariationRequest;

class CreateCategoryVariationDto
{
    public function __construct(
        private readonly string $categoryUUID,
        private readonly string $name,
        private ?string $categoryId = null,
    ) {}

    public static function fromRequest(CategoryVariationRequest $request): self
    {
        return new self(
            categoryUUID: $request->input('category_id'),
            name: $request->input('name'),
        );
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->categoryId,
            'name' => $this->name,
        ];
    }

    public function getCategoryUUID(): string
    {
        return $this->categoryUUID;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setCategoryId(string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }
}
