<?php

namespace App\Infrastructure\Repositories\Cart;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Domain\Order\Dtos\CartDto;
use App\Domain\Order\Interfaces\Cart\UserCartItemRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class UserCartItemRepository extends BaseRepository implements UserCartItemRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator
    {
        return UserCartItem::with(
            'cart',
            'productItem',
            'productItem.product',
            'productItem.variationOption',
            'productItem.firstImage',
        )->whereHas('cart', function ($query) {
            $query->where('status', CartStatusEnum::PENDING->value)
                ->where('user_id', auth()->user()->id);
        })->where('status', CartStatusEnum::PENDING->value)
            ->latest()
            ->paginate(50);
    }

    public function storeOrUpdate(CartDto $addToCartDto): UserCartItem
    {
        return UserCartItem::updateOrCreate(
            ['cart_id' => $addToCartDto->getCartId(), 'product_item_id' => $addToCartDto->getProductItemId()],
            $addToCartDto->toCartItemArray(),
        );
    }

    public function completeCartItems(int $cartId, string $status): bool
    {
        return self::findAllByColumn(
            UserCartItem::class,
            'cart_id',
            $cartId,
        )->lockForUpdate()
            ->update(compact('status'));
    }

    public function findExistingCartItem(?int $cartId, int $productItemId): ?UserCartItem
    {
        return UserCartItem::query()
            ->where('cart_id', $cartId)
            ->where('product_item_id', $productItemId)
            ->first();
    }
}
