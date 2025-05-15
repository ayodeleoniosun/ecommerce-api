<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Repositories\BaseRepository;

class ProductItemRepository extends BaseRepository implements ProductItemRepositoryInterface
{
    public function storeOrUpdate(CreateOrUpdateProductItemDto $createOrUpdateProductItemDto): ProductItem
    {
        $existingProductItem = $this->findExistingProductItem($createOrUpdateProductItemDto->getProductId(),
            $createOrUpdateProductItemDto->getCategoryVariationOptionId());

        if ($existingProductItem || $createOrUpdateProductItemDto->getProductItemId()) {
            $searchToUpdateBy = [
                'id' => $existingProductItem?->id ?? $createOrUpdateProductItemDto->getProductItemId(),
            ];
        } else {
            $searchToUpdateBy = [
                'product_id' => $createOrUpdateProductItemDto->getProductId(),
                'variation_option_id' => $createOrUpdateProductItemDto->getCategoryVariationOptionId(),
            ];
        }

        $productItem = ProductItem::updateOrCreate($searchToUpdateBy, $createOrUpdateProductItemDto->toArray());

        $productItem->load('product', 'variationOption');

        return $productItem;
    }

    public function findExistingProductItem(int $productId, string $variationOptionId): ?ProductItem
    {
        return ProductItem::where('product_id', $productId)
            ->where('variation_option_id', $variationOptionId)
            ->first();
    }

    public function lockItem(int $productItemId): ?ProductItem
    {
        return ProductItem::where('id', $productItemId)
            ->lockForUpdate()
            ->first();
    }

    public function decreaseStock(ProductItem $productItem, int $quantity): bool|int
    {
        return $productItem->decrement('quantity', $quantity);
    }
}
