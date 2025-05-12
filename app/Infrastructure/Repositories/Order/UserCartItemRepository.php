<?php

namespace App\Infrastructure\Repositories\Order;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Infrastructure\Models\UserCartItem;
use App\Infrastructure\Repositories\Inventory\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class UserCartItemRepository extends BaseRepository implements UserCartItemRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search') ?? null;

        $cartItems = UserCartItem::with(
            'cart',
            'productItem',
            'productItem.product',
            'productItem.variationOption',
            'productItem.firstImage',
        )->latest();

        return $cartItems->paginate(50);
    }

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
