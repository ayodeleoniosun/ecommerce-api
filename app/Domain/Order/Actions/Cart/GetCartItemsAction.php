<?php

namespace App\Domain\Order\Actions\Cart;

use App\Domain\Order\Interfaces\Cart\UserCartItemRepositoryInterface;
use App\Domain\Order\Resources\Cart\CartResourceCollection;
use Illuminate\Http\Request;

class GetCartItemsAction
{
    public function __construct(
        private readonly UserCartItemRepositoryInterface $userCartItemRepository,
    ) {}

    public function execute(Request $request): CartResourceCollection
    {
        return new CartResourceCollection($this->userCartItemRepository->index($request));
    }
}
