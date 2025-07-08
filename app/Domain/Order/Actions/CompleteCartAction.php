<?php

namespace App\Domain\Order\Actions;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Application\Shared\Enum\OrderStatusEnum;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CompleteCartAction
{
    public function __construct(
        private readonly UserCartRepositoryInterface $userCartRepository,
        private readonly UserCartItemRepositoryInterface $userCartItemRepository,
    ) {}

    public function execute(string $status): void
    {
        $cart = $this->userCartRepository->findPendingCart(auth()->user()->id);

        throw_if(! $cart, ResourceNotFoundException::class, 'You do not have any existing cart');

        $cartStatus = $status === OrderStatusEnum::SUCCESS->value ? CartStatusEnum::CHECKED_OUT->value : $cart->status;

        DB::transaction(function () use ($cart, $cartStatus) {
            $this->userCartRepository->updateByColumns(
                $cart,
                ['status' => $cartStatus],
            );

            $this->userCartItemRepository->completeCartItems($cart->id, $cartStatus);
        });
    }
}
