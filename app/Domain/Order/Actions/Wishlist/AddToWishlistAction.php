<?php

namespace App\Domain\Order\Actions\Wishlist;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Dtos\WishlistDto;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Domain\Order\Resources\Cart\WishlistResource;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Infrastructure\Models\Inventory\ProductItem;

class AddToWishlistAction
{
    public function __construct(
        private readonly ProductItemRepositoryInterface $productItemRepository,
        private readonly WishlistRepositoryInterface $wishlistRepository,
    ) {}

    public function execute(WishlistDto $wishlistDto): WishlistResource
    {
        $productItem = $this->productItemRepository->findByColumn(
            ProductItem::class,
            'uuid',
            $wishlistDto->getProductItemUUID(),
        );

        throw_if(! $productItem, ResourceNotFoundException::class, 'Product item not found');

        throw_if($productItem->quantity === 0, BadRequestException::class, 'Product item is out of stock');

        $existingWishlist = $this->wishlistRepository->findExistingWishlist($productItem->id);

        throw_if($existingWishlist, BadRequestException::class, 'Item already added to your wishlist');

        $wishlist = $this->wishlistRepository->create($wishlistDto->toArray());

        return new WishlistResource($wishlist);
    }
}
