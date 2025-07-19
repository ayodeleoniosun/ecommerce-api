<?php

namespace App\Domain\Order\Actions\Cart\Wishlist;

use App\Application\Shared\Enum\WishlistStatusEnum;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Infrastructure\Models\Cart\Wishlist;

class RemoveWishlistItemAction
{
    public function __construct(
        private readonly WishlistRepositoryInterface $wishlistRepository,
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function execute(string $wishlistUUID): bool
    {
        $wishlist = $this->wishlistRepository->findByColumn(
            Wishlist::class,
            'uuid',
            $wishlistUUID,
        );

        $existingWishlist = $this->wishlistRepository->findExistingWishlist($wishlist?->product_item_id);

        if (! $existingWishlist || $existingWishlist->status === WishlistStatusEnum::ADDED_TO_CART->value) {
            throw new ResourceNotFoundException('Item not found in your wishlist');
        }

        return $this->wishlistRepository->delete($existingWishlist);
    }
}
