<?php

namespace App\Domain\Order\Actions\Wishlist;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Actions\Cart\AddToCartAction;
use App\Domain\Order\Dtos\CartDto;
use App\Domain\Order\Enums\CartOperationEnum;
use App\Domain\Order\Enums\WishlistStatusEnum;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Domain\Order\Resources\Cart\CartResource;
use Illuminate\Support\Facades\DB;

class AddWishlistItemToCartAction extends BaseWishlistAction
{
    public function __construct(
        protected WishlistRepositoryInterface $wishlistRepository,
        private readonly AddToCartAction $addToCart,
    ) {
        parent::__construct($this->wishlistRepository);
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function execute(string $wishlistUUID): CartResource
    {
        $wishlist = $this->validateWishlist($wishlistUUID);

        $cartDto = new CartDto(
            productItemUUID: $wishlist->productItem->uuid,
            productItemId: $wishlist->product_item_id,
            currency: $wishlist->productItem->currency,
            quantity: 1,
            type: CartOperationEnum::INCREMENT->value,
            userId: $wishlist->user_id,
        );

        return DB::transaction(function () use ($wishlist, $cartDto) {
            $this->wishlistRepository->updateColumns($wishlist, [
                'status' => WishlistStatusEnum::ADDED_TO_CART->value,
            ]);

            return $this->addToCart->execute($cartDto);
        });
    }
}
