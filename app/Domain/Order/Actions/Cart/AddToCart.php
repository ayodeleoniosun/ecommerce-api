<?php

namespace App\Domain\Order\Actions\Cart;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use App\Domain\Order\Resources\CartResource;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Infrastructure\Models\ProductItem;
use Illuminate\Support\Facades\DB;

class AddToCart
{
    public function __construct(
        private readonly ProductItemRepositoryInterface $productItemRepository,
        private readonly UserCartRepositoryInterface $userCartRepository,
        private readonly UserCartItemRepositoryInterface $userCartItemRepository,
    ) {}

    public function execute(AddToCartDto $addToCartDto): CartResource
    {
        $productItem = $this->productItemRepository->findByColumn(ProductItem::class, 'uuid',
            $addToCartDto->getProductItemUUID());

        throw_if(! $productItem, ResourceNotFoundException::class, 'Product item not found');

        $incrementedQuantity = $this->getQuantity($addToCartDto->getQuantity(), $addToCartDto->getUserId(),
            $productItem->id);

        throw_if($incrementedQuantity > $productItem->quantity, BadRequestException::class,
            'Insufficient product quantity');

        $addToCartDto->setQuantity($incrementedQuantity);

        return DB::transaction(function () use ($addToCartDto) {
            $cart = $this->userCartRepository->findOrCreate($addToCartDto);

            $addToCartDto->setCartId($cart->id);

            $cartItem = $this->userCartItemRepository->storeOrUpdate($addToCartDto);

            return new CartResource($cartItem->load('productItem', 'productItem.product', 'productItem.variationOption',
                'cart'));
        });
    }

    public function getQuantity(int $quantity, int $userId, int $productItemId): int
    {
        $existingCartItem = null;

        $existingCart = $this->userCartRepository->findPendingCart($userId);

        if ($existingCart) {
            $existingCartItem = $this->userCartItemRepository->findExistingCartItem($existingCart->id,
                $productItemId);
        }

        if ($existingCartItem) {
            return $existingCartItem->quantity + $quantity;
        }

        return $quantity;
    }
}
