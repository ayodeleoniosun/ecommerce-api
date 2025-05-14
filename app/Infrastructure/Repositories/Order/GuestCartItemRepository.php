<?php

namespace App\Infrastructure\Repositories\Order;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\GuestCartItemRepositoryInterface;
use App\Infrastructure\Models\Order\GuestCartItem;
use App\Infrastructure\Repositories\Inventory\BaseRepository;

class GuestCartItemRepository extends BaseRepository implements GuestCartItemRepositoryInterface
{
    public function storeOrUpdate(AddToCartDto $addToCartDto): GuestCartItem
    {
        return GuestCartItem::updateOrCreate(
            ['cart_id' => $addToCartDto->getCartId(), 'product_item_id' => $addToCartDto->getProductItemId()],
            $addToCartDto->toCartItemArray(),
        );
    }
}
