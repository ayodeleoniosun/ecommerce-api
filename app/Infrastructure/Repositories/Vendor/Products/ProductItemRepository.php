<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Infrastructure\Models\ProductItem;
use App\Infrastructure\Repositories\Inventory\BaseRepository;

class ProductItemRepository extends BaseRepository implements ProductItemRepositoryInterface
{
    public function storeOrUpdate(CreateOrUpdateProductItemDto $createOrUpdateProductItemDto): ProductItem
    {
        if ($createOrUpdateProductItemDto->getProductItemId()) {
            $searchToUpdateBy = [
                'id' => $createOrUpdateProductItemDto->getProductItemId(),
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
}
