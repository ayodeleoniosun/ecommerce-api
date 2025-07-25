<?php

namespace App\Domain\Order\Actions\Wishlist;

use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Domain\Order\Resources\Cart\WishlistResourceCollection;

class GetWishlistItemsAction
{
    public function __construct(
        private readonly WishlistRepositoryInterface $wishlistRepository,
    ) {}

    public function execute(): WishlistResourceCollection
    {
        return new WishlistResourceCollection($this->wishlistRepository->index());
    }
}
