<?php

namespace App\Domain\Order\Actions\Cart;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Enums\CartStatusEnum;
use App\Domain\Order\Interfaces\Cart\UserCartItemRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCartItem;

class RemoveCartItemAction
{
    public function __construct(
        private readonly UserCartItemRepositoryInterface $userCartItemRepository,
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function execute(string $cartItemUUID): bool
    {
        $cartItem = $this->userCartItemRepository->findByColumn(UserCartItem::class, 'uuid', $cartItemUUID);

        if (! $this->canRemoveCartItem($cartItem)) {
            throw new ResourceNotFoundException('Item not found in your cart');
        }

        return $this->userCartItemRepository->delete($cartItem);
    }

    private function canRemoveCartItem($cartItem): bool
    {
        return $cartItem && $cartItem->status === CartStatusEnum::PENDING->value && $cartItem->cart->user_id === auth()->user()->id;
    }
}
