<?php

namespace App\Domain\Order\Actions;

use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Domain\Order\Resources\Cart\CartResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetCartItems
{
    public function __construct(
        private readonly UserCartItemRepositoryInterface $userCartItemRepository,
    ) {}

    public function execute(Request $request): AnonymousResourceCollection
    {
        return CartResource::collection($this->userCartItemRepository->index($request));
    }
}
