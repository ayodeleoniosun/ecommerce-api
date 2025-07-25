<?php

namespace Tests\Unit\Order\Order;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Domain\Order\Actions\Order\GetOrdersAction;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Order\Resources\Order\OrderResourceCollection;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Order\OrderShipping;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

it('should get orders', function () {
    $orderRepo = Mockery::mock(OrderRepositoryInterface::class);
    $currency = CurrencyEnum::NGN->value;

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

    $userCarts = UserCart::factory()
        ->count(3)
        ->create([
            'user_id' => $user->id,
        ]);

    $allOrders = Order::factory()
        ->count(3)
        ->state(new Sequence(
            ['cart_id' => $userCarts[0]->id],
            ['cart_id' => $userCarts[1]->id, 'status' => OrderStatusEnum::SUCCESS->value],
            ['cart_id' => $userCarts[2]->id],
        ))->create([
            'user_id' => $user->id,
        ]);

    $userCartItems = UserCartItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_item_id' => $productItems[0]->id, 'cart_id' => $userCarts[0]->id, 'quantity' => 2],
            ['product_item_id' => $productItems[1]->id, 'cart_id' => $userCarts[1]->id, 'quantity' => 3],
            ['product_item_id' => $productItems[2]->id, 'cart_id' => $userCarts[2]->id, 'quantity' => 5],
        ))->create();

    OrderItem::factory()->count(3)
        ->state(new Sequence(
            [
                'cart_item_id' => $userCartItems[0]->id,
                'order_id' => $allOrders[0]->id,
                'total_amount' => $userCartItems[0]->quantity * $productItems[0]->price,
            ],
            [
                'cart_item_id' => $userCartItems[1]->id,
                'order_id' => $allOrders[1]->id,
                'total_amount' => $userCartItems[1]->quantity * $productItems[1]->price,
            ],
            [
                'cart_item_id' => $userCartItems[2]->id,
                'order_id' => $allOrders[2]->id,
                'total_amount' => $userCartItems[2]->quantity * $productItems[2]->price,
            ],
        ))->create();

    OrderShipping::factory()
        ->count(3)
        ->state(new Sequence(
            ['order_id' => $allOrders[0]->id],
            ['order_id' => $allOrders[1]->id],
            ['order_id' => $allOrders[2]->id]
        ))->create();

    OrderPayment::factory()
        ->count(3)
        ->state(new Sequence(
            ['order_id' => $allOrders[0]->id],
            ['order_id' => $allOrders[1]->id],
            ['order_id' => $allOrders[2]->id]
        ))->create();

    $this->actingAs($user, 'sanctum');

    $getOrder = new GetOrdersAction($orderRepo);

    $ordersPaginatedData = new LengthAwarePaginator(
        items: $allOrders,
        total: 2,
        perPage: 50,
        currentPage: 1,
    );

    $orderRepo->shouldReceive('index')
        ->once()
        ->with($currency)
        ->andReturn($ordersPaginatedData);

    $response = $getOrder->execute($currency);
    $items = collect($response->resource->items());

    expect($response)->toBeInstanceOf(OrderResourceCollection::class)
        ->and($response->collection)->toHaveCount(3)
        ->and($items->every(fn ($item) => $item->user_id === $user->id))->toBeTrue()
        ->and($items->every(fn ($item) => $item->currency === $currency))->toBeTrue()
        ->and($items->map(fn ($item) => $item->cart_id)->all())->toEqual([
            $userCarts[0]->id,
            $userCarts[1]->id,
            $userCarts[2]->id,
        ])->and($items->map(fn ($item) => $item->status)->all())->toEqual([
            OrderStatusEnum::PENDING->value,
            OrderStatusEnum::SUCCESS->value,
            OrderStatusEnum::PENDING->value,
        ]);
});
