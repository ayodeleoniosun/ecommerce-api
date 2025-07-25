<?php

namespace Tests\Unit\Order\Wishlist;

use App\Domain\Order\Actions\Wishlist\GetWishlistItemsAction;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Domain\Order\Resources\Cart\WishlistResourceCollection;
use App\Infrastructure\Models\Cart\Wishlist;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

it('should get wishlist items', function () {
    $wishlistRepo = Mockery::mock(WishlistRepositoryInterface::class);

    $user = User::factory()->create();
    $product = Product::factory()->create();

    $productItems = ProductItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['price' => 10000, 'quantity' => 10],
            ['price' => 20000, 'quantity' => 15],
            ['price' => 30000, 'quantity' => 20],
        ))->create([
            'product_id' => $product->id,
        ]);

    $wishlists = Wishlist::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_item_id' => $productItems[0]->id],
            ['product_item_id' => $productItems[1]->id],
            ['product_item_id' => $productItems[2]->id],
        ))->create([
            'user_id' => $user->id,
        ]);

    $this->actingAs($user, 'sanctum');

    $getWishlistItems = new GetWishlistItemsAction($wishlistRepo);

    $wishlistsPaginatedData = new LengthAwarePaginator(
        items: $wishlists,
        total: 2,
        perPage: 50,
        currentPage: 1,
    );

    $wishlistRepo->shouldReceive('index')
        ->once()
        ->andReturn($wishlistsPaginatedData);

    $response = $getWishlistItems->execute();
    $items = collect($response->resource->items());

    expect($response)->toBeInstanceOf(WishlistResourceCollection::class)
        ->and($response->collection)->toHaveCount(3)
        ->and($items->every(fn ($item) => $item->user_id === $user->id))->toBeTrue()
        ->and($items->map(fn ($item) => $item->product_item_id)->all())->toEqual([
            $productItems[0]->id,
            $productItems[1]->id,
            $productItems[2]->id,
        ]);
});
