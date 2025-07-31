<?php

namespace App\Domain\Vendor\Products\Listeners;

use App\Domain\Order\Enums\CartStatusEnum;
use App\Domain\Order\Interfaces\Cart\UserCartItemRepositoryInterface;
use App\Domain\Vendor\Products\Events\CartItemsRestocked;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class RestockCartItems implements ShouldQueue
{
    /**s
     * Create the event listener.
     */
    public function __construct(
        public readonly UserCartItemRepositoryInterface $userCartItemRepository,
        public readonly ProductItemRepositoryInterface $productItemRepository,
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CartItemsRestocked $event)
    {
        $totalQuantity = 0;
        $productItem = $event->productItem;
        $cartItems = $this->userCartItemRepository->getAllOutOfStockItems($productItem->id);
        $lockedProductItem = $this->productItemRepository->lockForUpdate($productItem);

        $cartItems->takeUntil(function ($item) use (&$totalQuantity, $lockedProductItem) {
            $totalQuantity += $item->quantity;

            return $totalQuantity >= $lockedProductItem->quantity;
        })->each(function ($item) use ($lockedProductItem) {
            DB::transaction(function () use ($lockedProductItem, $item) {
                $this->productItemRepository->decreaseStock($lockedProductItem, $item->quantity);

                $this->userCartItemRepository->updateColumns($item, [
                    'status' => CartStatusEnum::PENDING->value,
                ]);
            });
        });
    }
}
