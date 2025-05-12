<?php

namespace App\Domain\Order\Interfaces;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Infrastructure\Models\UserCartItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface UserCartItemRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator;

    public function storeOrUpdate(AddToCartDto $addToCartDto): UserCartItem;

    public function findExistingCartItem(int $cartId, int $productItemId): ?UserCartItem;
}
