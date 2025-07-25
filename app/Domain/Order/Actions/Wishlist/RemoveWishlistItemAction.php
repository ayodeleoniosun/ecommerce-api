<?php

namespace App\Domain\Order\Actions\Wishlist;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;

class RemoveWishlistItemAction extends BaseWishlistAction
{
    public function __construct(
        protected WishlistRepositoryInterface $wishlistRepository,
    ) {
        parent::__construct($this->wishlistRepository);
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function execute(string $wishlistUUID): bool
    {
        $existingWishlist = $this->validateWishlist($wishlistUUID);

        return $this->wishlistRepository->delete($existingWishlist);
    }
}
