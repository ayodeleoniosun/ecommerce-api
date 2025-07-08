<?php

namespace Tests\Unit\Order;

use App\Domain\Order\Actions\GetCartItems;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

it('should get cart items', function () {
    $userCartItemRepo = Mockery::mock(UserCartItemRepositoryInterface::class);
    $request = Mockery::mock(Request::class);

    $user = User::factory()->create();

    $userCart = UserCart::factory()->create([
        'user_id' => $user->id,
    ]);

    $product = Product::factory()->create();

    $productItem = ProductItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_id' => $product->id, 'price' => 10000, 'quantity' => 10],
            ['product_id' => $product->id, 'price' => 20000, 'quantity' => 15],
            ['product_id' => $product->id, 'price' => 30000, 'quantity' => 20],
        ))->create();

    $userCartItems = UserCartItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_item_id' => $productItem[0]->id, 'quantity' => 2],
            ['product_item_id' => $productItem[1]->id, 'quantity' => 3],
            ['product_item_id' => $productItem[2]->id, 'quantity' => 5],
        ))
        ->create([
            'cart_id' => $userCart->id,
        ]);

    $this->actingAs($user, 'sanctum');

    $cartItems = new GetCartItems($userCartItemRepo);

    $userCartItemsPaginatedData = new LengthAwarePaginator(
        items: $userCartItems,
        total: 2,
        perPage: 50,
        currentPage: 1,
    );

    $userCartItemRepo->shouldReceive('index')
        ->once()
        ->with($request)
        ->andReturn($userCartItemsPaginatedData);

    $response = $cartItems->execute($request);

    expect($response)->toBeInstanceOf(AnonymousResourceCollection::class)
        ->and($response->collection)->toHaveCount(3);
});
