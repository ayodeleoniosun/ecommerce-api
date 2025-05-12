<?php

namespace App\Domain\Order\Actions\Cart;

use App\Application\Shared\Enum\CartOperationEnum;
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
        $productItem = $this->productItemRepository->findByColumn(
            ProductItem::class,
            'uuid',
            $addToCartDto->getProductItemUUID(),
        );

        throw_if(! $productItem, ResourceNotFoundException::class, 'Product item not found');

        return DB::transaction(function () use ($productItem, $addToCartDto) {
            $lockedItem = $this->productItemRepository->lockItem($productItem->id);

            $cartQuantity = $addToCartDto->getQuantity();

            throw_if(
                $cartQuantity > $lockedItem->quantity,
                BadRequestException::class,
                'Insufficient product quantity',
            );

            $updatedQuantity = $this->updateQuantity($addToCartDto);

            $addToCartDto->setQuantity($updatedQuantity);

            $cart = $this->userCartRepository->findOrCreate($addToCartDto);

            $addToCartDto->setCartId($cart->id);

            $cartItem = $this->userCartItemRepository->storeOrUpdate($addToCartDto);

            $this->productItemRepository->decreaseStock($lockedItem, $cartQuantity);

            return new CartResource($cartItem->load('productItem', 'productItem.product', 'productItem.variationOption',
                'cart'));
        });
    }

    public function updateQuantity(AddToCartDto $addToCartDto): int
    {
        $existingCart = $this->userCartRepository->findPendingCart($addToCartDto->getUserId());

        $existingCartItem = $this->userCartItemRepository->findExistingCartItem($existingCart?->id,
            $addToCartDto->getProductItemId());

        if ($existingCartItem) {
            if ($addToCartDto->getType() === CartOperationEnum::INCREMENT->value) {
                return $existingCartItem->quantity + $addToCartDto->getQuantity();
            }

            if ($addToCartDto->getType() === CartOperationEnum::DECREMENT->value) {
                return $existingCartItem->quantity - $addToCartDto->getQuantity();
            }
        }

        return $addToCartDto->getQuantity();
    }
}
