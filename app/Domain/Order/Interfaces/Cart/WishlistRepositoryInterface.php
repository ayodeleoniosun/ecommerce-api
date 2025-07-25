<?php

namespace App\Domain\Order\Interfaces\Cart;

use App\Infrastructure\Models\Cart\Wishlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WishlistRepositoryInterface
{
    public function index(): LengthAwarePaginator;

    public function create(array $data): Wishlist;

    public function findExistingWishlist(?int $productItemId): ?Wishlist;
}
