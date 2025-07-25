<?php

namespace Tests\Unit\Order\Wishlist;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Actions\Wishlist\RemoveWishlistItemAction;
use App\Domain\Order\Enums\WishlistStatusEnum;
use App\Infrastructure\Models\Cart\Wishlist;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Cart\WishlistRepository;
use Mockery;

beforeEach(function () {
    $this->wishlistRepo = Mockery::mock(WishlistRepository::class)->makePartial();

    $this->user = User::factory()->create();
    $this->product = Product::factory()->create();

    $this->productItem = ProductItem::factory()->create([
        'product_id' => $this->product->id,
        'price' => 10000,
        'quantity' => 10,
    ]);

    $this->wishlist = Wishlist::factory()->create([
        'user_id' => $this->user->id,
        'product_item_id' => $this->productItem->id,
    ]);

    $this->actingAs($this->user, 'sanctum');

    $this->removeWishlistItem = new RemoveWishlistItemAction($this->wishlistRepo);
});

it('should throw an exception if wishlist item is not found', function () {
    $this->removeWishlistItem->execute('invalid-uuid');
})->throws(ResourceNotFoundException::class, 'Item not found in your wishlist');

it('should throw an exception if wishlist item has already been added to cart', function () {
    $this->wishlist->status = WishlistStatusEnum::ADDED_TO_CART->value;
    $this->wishlist->save();

    $this->removeWishlistItem->execute($this->wishlist->uuid);
})->throws(ResourceNotFoundException::class, 'Item not found in your wishlist');

it('should remove an item from wishlist', function () {
    $response = $this->removeWishlistItem->execute($this->wishlist->uuid);

    expect($response)->toBeTrue();
});
