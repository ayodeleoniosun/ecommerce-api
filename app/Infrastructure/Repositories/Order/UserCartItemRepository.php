<?php

namespace App\Infrastructure\Repositories\Order;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Infrastructure\Models\UserCartItem;
use App\Infrastructure\Repositories\Inventory\BaseRepository;

class UserCartItemRepository extends BaseRepository implements UserCartItemRepositoryInterface
{
    public function storeOrUpdate(AddToCartDto $addToCartDto): UserCartItem
    {
        return UserCartItem::updateOrCreate(
            ['cart_id' => $addToCartDto->getCartId(), 'product_item_id' => $addToCartDto->getProductItemId()],
            $addToCartDto->toCartItemArray(),
        );
    }

    public function findExistingCartItem(int $cartId, int $productItemId): ?UserCartItem
    {
        return UserCartItem::query()
            ->where('cart_id', $cartId)
            ->where('product_item_id', $productItemId)
            ->first();
    }
}
