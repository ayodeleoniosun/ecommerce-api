<?php

namespace App\Domain\Order\Actions\Cart;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Infrastructure\Models\Order\UserCartItem;

class RemoveCartItem
{
    public function __construct(
        private readonly UserCartItemRepositoryInterface $userCartItemRepository,
    ) {}

    public function execute(string $cartItemUUID): bool
    {
        $cartItem = $this->userCartItemRepository->findByColumn(UserCartItem::class, 'uuid', $cartItemUUID);

        throw_if(! $cartItem, ResourceNotFoundException::class, 'Item not found in cart');

        return $this->userCartItemRepository->delete($cartItem);
    }
}
