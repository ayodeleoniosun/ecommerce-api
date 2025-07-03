<?php

namespace App\Domain\Order\Actions\Cart;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCartItem;

class RemoveCartItem
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

        if (! $cartItem || $cartItem->status !== CartStatusEnum::PENDING->value) {
            throw new ResourceNotFoundException('Item not found in cart');
        }

        return $this->userCartItemRepository->delete($cartItem);
    }
}
