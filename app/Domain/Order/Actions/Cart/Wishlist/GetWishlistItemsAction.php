<?php

namespace App\Domain\Order\Actions\Cart\Wishlist;

use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Domain\Order\Resources\Cart\WishlistResourceCollection;
use Illuminate\Http\Request;

class GetWishlistItemsAction
{
    public function __construct(
        private readonly WishlistRepositoryInterface $wishlistRepository,
    ) {}

    public function execute(Request $request): WishlistResourceCollection
    {
        return new WishlistResourceCollection($this->wishlistRepository->index($request));
    }
}
