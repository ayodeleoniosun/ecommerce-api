<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductItemResource;

class CreateOrUpdateProductItem
{
    public function __construct(
        private readonly ProductItemRepositoryInterface $productItemRepository,
    ) {}

    public function execute(CreateOrUpdateProductItemDto $createOrUpdateProductItemDto): array
    {
        $isExist = (bool) $this->productItemRepository->findExistingProductItem($createOrUpdateProductItemDto->getProductId(),
            $createOrUpdateProductItemDto->getCategoryVariationOptionId());
        $isUpdating = (bool) $createOrUpdateProductItemDto->getProductItemId();

        $productItem = $this->productItemRepository->storeOrUpdate($createOrUpdateProductItemDto);

        return [
            'is_existing_product_item' => $isExist || $isUpdating,
            'product_item' => new ProductItemResource($productItem),
        ];
    }
}
