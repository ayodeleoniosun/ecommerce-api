<?php

namespace App\Infrastructure\Repositories\Cart;

use App\Domain\Order\Enums\WishlistStatusEnum;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Infrastructure\Models\Cart\Wishlist;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WishlistRepository extends BaseRepository implements WishlistRepositoryInterface
{
    public function index(): LengthAwarePaginator
    {
        return Wishlist::with(
            'productItem',
            'productItem.product',
            'productItem.variationOption',
            'productItem.firstImage',
        )->whereNot('status', WishlistStatusEnum::ADDED_TO_CART->value)
            ->latest()
            ->paginate(50);
    }

    public function create(array $data): Wishlist
    {
        return Wishlist::create($data);
    }

    public function findExistingWishlist(?int $productItemId): ?Wishlist
    {
        return Wishlist::where('user_id', auth()->user()->id)
            ->where('product_item_id', $productItemId)
            ->where('status', WishlistStatusEnum::IN_STOCK->value)
            ->first();
    }
}
