<?php

namespace App\Domain\Order\Actions;

use App\Application\Shared\Enum\CartOperationEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use App\Domain\Order\Resources\Cart\CartResource;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\ProductItem;
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

        $cartItem = null;

        DB::transaction(function () use ($productItem, $addToCartDto, &$cartItem) {
            $lockedProductItem = $this->productItemRepository->lockItem($productItem->id);

            $cartQuantity = $addToCartDto->getQuantity();

            throw_if(
                $cartQuantity > $lockedProductItem->quantity,
                BadRequestException::class,
                'Insufficient product quantity',
            );

            $this->updateQuantity($addToCartDto);

            $cart = $this->userCartRepository->findOrCreate($addToCartDto);

            $addToCartDto->setCartId($cart->id);

            $existingCartItem = $this->userCartItemRepository->findExistingCartItem(
                $addToCartDto->getCartId(),
                $addToCartDto->getProductItemId(),
            );

            $this->handleStockQuantity($cartQuantity, $existingCartItem, $lockedProductItem, $addToCartDto);

            $cartItem = $this->userCartItemRepository->storeOrUpdate($addToCartDto);

            if ($cartItem->quantity === 0) {
                $this->removeCartItem($cartItem);
            }
        });

        $cartItemRecord = $cartItem->load(
            'productItem',
            'productItem.product',
            'productItem.variationOption',
            'cart',
        );

        return new CartResource($cartItemRecord);
    }

    private function updateQuantity(AddToCartDto $addToCartDto): void
    {
        $existingCart = $this->userCartRepository->findPendingCart($addToCartDto->getUserId());

        $existingCartItem = $this->userCartItemRepository->findExistingCartItem($existingCart?->id,
            $addToCartDto->getProductItemId());

        $quantity = $addToCartDto->getQuantity();

        if ($existingCartItem) {
            if ($addToCartDto->getType() === CartOperationEnum::INCREMENT->value) {
                $quantity = $existingCartItem->quantity + $addToCartDto->getQuantity();
            }

            if ($addToCartDto->getType() === CartOperationEnum::DECREMENT->value) {
                $quantity = $existingCartItem->quantity - $addToCartDto->getQuantity();
            }
        }

        $addToCartDto->setQuantity($quantity);
    }

    /**
     * @throws BadRequestException
     */
    private function handleStockQuantity(
        int $cartQuantity,
        ?UserCartItem $cartItem,
        ProductItem $productItem,
        AddToCartDto $addToCartDto,
    ): void {
        if ($addToCartDto->getType() === CartOperationEnum::INCREMENT->value) {
            $this->productItemRepository->decreaseStock($productItem, $cartQuantity);

            return;
        }

        if (! $cartItem) {
            throw new BadRequestException('You cannot decrement a non-existing cart item.');
        }

        if ($cartQuantity > $cartItem?->quantity) {
            throw new BadRequestException('Cart item quantity is lesser than the quantity to be decremented');
        }

        $this->productItemRepository->increaseStock($productItem, $cartQuantity);
    }

    private function removeCartItem(UserCartItem $cartItem): void
    {
        $cartItem->refresh();

        if ($cartItem->quantity === 0) {
            $cartItem->delete();
        }
    }
}
