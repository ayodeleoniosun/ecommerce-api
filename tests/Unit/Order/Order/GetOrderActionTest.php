<?php

namespace Tests\Unit\Order\Order;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Actions\Order\GetOrderAction;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Order\OrderShipping;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Order\OrderRepository;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Mockery;

beforeEach(function () {
    $this->orderRepo = Mockery::mock(OrderRepository::class)->makePartial();
    $this->user = User::factory()->create();
    $this->product = Product::factory()->create();

    $this->productItems = ProductItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['price' => 10000, 'quantity' => 10],
            ['price' => 20000, 'quantity' => 15],
            ['price' => 30000, 'quantity' => 20],
        ))->create([
            'product_id' => $this->product->id,
        ]);

    $this->userCart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->order = Order::factory()->create([
        'user_id' => $this->user->id,
        'cart_id' => $this->userCart->id,
        'status' => OrderStatusEnum::SUCCESS->value,
    ]);

    $this->userCartItems = UserCartItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_item_id' => $this->productItems[0]->id, 'quantity' => 2],
            ['product_item_id' => $this->productItems[1]->id, 'quantity' => 3],
            ['product_item_id' => $this->productItems[2]->id, 'quantity' => 5],
        ))->create([
            'cart_id' => $this->userCart->id,
        ]);

    $this->orderItems = OrderItem::factory()
        ->count(3)
        ->state(new Sequence(
            [
                'cart_item_id' => $this->userCartItems[0]->id,
                'total_amount' => $this->userCartItems[0]->quantity * $this->productItems[0]->price,
            ],
            [
                'cart_item_id' => $this->userCartItems[1]->id,
                'total_amount' => $this->userCartItems[1]->quantity * $this->productItems[1]->price,
            ],
            [
                'cart_item_id' => $this->userCartItems[2]->id,
                'total_amount' => $this->userCartItems[2]->quantity * $this->productItems[2]->price,
            ],
        ))->create([
            'order_id' => $this->order->id,
        ]);

    $this->orderShipping = OrderShipping::factory()->create([
        'order_id' => $this->order->id,
    ]);

    $this->orderPayment = OrderPayment::factory()->create([
        'order_id' => $this->order->id,
    ]);

    $this->actingAs($this->user, 'sanctum');

    $this->getOrder = new GetOrderAction($this->orderRepo);
});

it('should throw an exception if order is invalid', function () {
    $this->getOrder->execute('invalid_uuid');
})->throws(ResourceNotFoundException::class, 'Invalid order');

it('should return a valid order record', function () {
    $response = $this->getOrder->execute($this->order->uuid);

    expect($response)->toBeInstanceOf(OrderResource::class)
        ->and($response->cart_id)->toEqual($this->userCart->id)
        ->and($response->user_id)->toEqual($this->user->id)
        ->and($response->items)->toHaveCount(3)
        ->and($response->items->every(fn ($item) => $item->order_id === $this->order->id))->toBeTrue()
        ->and($response->items->map(fn ($item) => $item->total_amount)->all())->toEqual([
            $this->orderItems[0]->total_amount,
            $this->orderItems[1]->total_amount,
            $this->orderItems[2]->total_amount,
        ]);
});
