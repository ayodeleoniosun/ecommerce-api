<?php

namespace App\Domain\Order\Actions\Wishlist;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Enums\WishlistStatusEnum;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Infrastructure\Models\Cart\Wishlist;
use Illuminate\Database\Eloquent\Model;

class BaseWishlistAction
{
    public function __construct(
        protected WishlistRepositoryInterface $wishlistRepository,
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    protected function validateWishlist(string $wishlistUUID): Model
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

        return $wishlist;
    }
}
