<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Domain\Vendor\Products\Dtos\CreateProductItemDto;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Infrastructure\Models\ProductItem;

class ProductItemRepository implements ProductItemRepositoryInterface
{
    public function store(CreateProductItemDto $createProductItemDto): ProductItem
    {
        $productItem = ProductItem::updateOrCreate(
            [
                'product_id' => $createProductItemDto->getProductId(),
                'variation_option_id' => $createProductItemDto->getCategoryVariationOptionId(),
            ],
            $createProductItemDto->toArray(),
        );

        $productItem->load('product', 'variationOption');

        return $productItem;
    }
}
