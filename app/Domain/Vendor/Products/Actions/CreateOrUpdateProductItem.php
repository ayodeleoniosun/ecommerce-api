<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductItemResource;
use App\Infrastructure\Models\ProductItem;

class CreateOrUpdateProductItem
{
    public function __construct(
        private readonly ProductItemRepositoryInterface $productItemRepository,
    ) {}

    public function execute(CreateOrUpdateProductItemDto $createProductItemDto): array
    {
        $existingProductItem = $this->productItemRepository->findByColumn(ProductItem::class, 'product_id',
            $createProductItemDto->getProductId());

        $productItem = $this->productItemRepository->storeOrUpdate($createProductItemDto);

        return [
            'is_existing_product_item' => (bool) $existingProductItem,
            'product_item' => new ProductItemResource($productItem),
        ];
    }
}
