<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Domain\Vendor\Products\Enums\ProductStatusEnum;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function increaseStock(Model $productItem, int $quantity): bool|int
    {
        return DB::transaction(function () use ($productItem, $quantity) {
            $productItem->status = ProductStatusEnum::IN_STOCK->value;
            $productItem->save();

            return $productItem->increment('quantity', $quantity);
        });
    }

    public function decreaseStock(Model $productItem, int $quantity): Model
    {
        return DB::transaction(function () use ($productItem, $quantity) {
            $productItem->decrement('quantity', $quantity);

            if ($productItem->quantity === 0) {
                $productItem->status = ProductStatusEnum::OUT_OF_STOCK->value;
                $productItem->save();
            }

            return $productItem;
        });
    }
}
