<?php

namespace App\Domain\Vendor\Products\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Http\UploadedFile;

class UploadProductItemImageDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $productItemUUID,
        private readonly UploadedFile $image,
        private readonly ?int $productItemId = null,
        private ?string $path = null,
        private ?string $uuid = null,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            productItemUUID: $payload['product_item_id'],
            image: $payload['image'],
            productItemId: $payload['merged_product_item_id'] ?? null,
            path: $payload['path'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'product_item_id' => $this->productItemId,
            'path' => $this->path,
        ];
    }

    public function getImage(): UploadedFile
    {
        return $this->image;
    }

    public function setImagePath(string $path): void
    {
        $this->path = $path;
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = $uuid;
    }
}
