<?php

namespace App\Domain\Order\Interfaces\Cart;

use App\Domain\Order\Dtos\CartDto;
use App\Infrastructure\Models\Cart\UserCartItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface UserCartItemRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator;

    public function storeOrUpdate(CartDto $addToCartDto): UserCartItem;

    public function completeCartItems(int $cartId, string $status): bool;

    public function findExistingCartItem(?int $cartId, int $productItemId): ?UserCartItem;

    public function getAllOutOfStockItems(int $productItemId): Collection;
}
