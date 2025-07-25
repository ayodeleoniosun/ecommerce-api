<?php

namespace Tests\Feature\Order;

use App\Domain\Auth\Enums\UserStatusEnum;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Shipping\Enums\DeliveryTypeEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Order\OrderShipping;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Response;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
        'status' => UserStatusEnum::ACTIVE->value,
    ]);

    $this->pickupStation = PickupStation::factory()->create();
    $this->customerShippingAddress = CustomerShippingAddress::factory()->create([
        'user_id' => $this->user->id,
    ]);

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

    $this->userCarts = UserCart::factory()
        ->count(3)
        ->create([
            'user_id' => $this->user->id,
        ]);

    $this->orders = Order::factory()
        ->count(3)
        ->state(new Sequence(
            ['cart_id' => $this->userCarts[0]->id],
            ['cart_id' => $this->userCarts[1]->id, 'status' => OrderStatusEnum::SUCCESS->value],
            ['cart_id' => $this->userCarts[2]->id],
        ))->create([
            'user_id' => $this->user->id,
        ]);

    $this->userCartItems = UserCartItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_item_id' => $this->productItems[0]->id, 'cart_id' => $this->userCarts[0]->id, 'quantity' => 2],
            ['product_item_id' => $this->productItems[1]->id, 'cart_id' => $this->userCarts[1]->id, 'quantity' => 3],
            ['product_item_id' => $this->productItems[2]->id, 'cart_id' => $this->userCarts[2]->id, 'quantity' => 5],
        ))->create();

    $this->orderItems = OrderItem::factory()->count(3)
        ->state(new Sequence(
            [
                'cart_item_id' => $this->userCartItems[0]->id,
                'order_id' => $this->orders[0]->id,
                'total_amount' => $this->userCartItems[0]->quantity * $this->productItems[0]->price,
            ],
            [
                'cart_item_id' => $this->userCartItems[1]->id,
                'order_id' => $this->orders[1]->id,
                'total_amount' => $this->userCartItems[1]->quantity * $this->productItems[1]->price,
            ],
            [
                'cart_item_id' => $this->userCartItems[2]->id,
                'order_id' => $this->orders[2]->id,
                'total_amount' => $this->userCartItems[2]->quantity * $this->productItems[2]->price,
            ],
        ))->create();

    OrderShipping::factory()
        ->count(3)
        ->state(new Sequence(
            ['order_id' => $this->orders[0]->id],
            ['order_id' => $this->orders[1]->id],
            ['order_id' => $this->orders[2]->id],
        ))->create();

    OrderPayment::factory()
        ->count(3)
        ->state(new Sequence(
            [
                'order_id' => $this->orders[0]->id,
                'order_amount' => $this->orderItems[0]->total_amount,
            ],
            [
                'order_id' => $this->orders[1]->id,
                'order_amount' => $this->orderItems[1]->total_amount,
                'amount_charged' => $this->orderItems[1]->total_amount,
            ],
            [
                'order_id' => $this->orders[2]->id,
                'order_amount' => $this->orderItems[2]->total_amount,
            ]
        ))->create();

    $this->actingAs($this->user, 'sanctum');
});

describe('checkout cart items', function () {
    it('should return an error if shipping address is invalid', function () {
        $payload = [
            'customer_address_id' => 'invalid_uuid',
            'pickup_station_id' => 'invalid_uuid',
            'delivery_type' => DeliveryTypeEnum::DOOR_DELIVERY->value,
            'payment_method' => PaymentTypeEnum::CARD->value,
        ];

        $response = $this->postJson('/api/orders', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Invalid customer address');
    });

    it('should return an error if pickup station is invalid', function () {
        $payload = [
            'customer_address_id' => $this->customerShippingAddress->uuid,
            'pickup_station_id' => 'invalid_uuid',
            'delivery_type' => DeliveryTypeEnum::DOOR_DELIVERY->value,
            'payment_method' => PaymentTypeEnum::CARD->value,
        ];

        $response = $this->postJson('/api/orders', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The selected pickup station id is invalid.');
    });

    it('should successfully checkout cart items', function () {
        $payload = [
            'customer_address_id' => $this->customerShippingAddress->uuid,
            'pickup_station_id' => $this->pickupStation->uuid,
            'delivery_type' => DeliveryTypeEnum::DOOR_DELIVERY->value,
            'payment_method' => PaymentTypeEnum::CARD->value,
        ];

        $response = $this->postJson('/api/orders', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Order created successfully')
            ->and($content->data->delivery_type)->toBe(DeliveryTypeEnum::DOOR_DELIVERY->value)
            ->and($content->data->amount)->toBe(20000)
            ->and($content->data->status)->toBe(OrderStatusEnum::PENDING->value);
    });
});

describe('get list of orders', function () {
    it('should return a list of all orders', function () {
        $response = $this->getJson('/api/orders/'.$this->orders[0]->currency);
        $content = json_decode($response->getContent());
        $items = collect($content->data->items);

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Orders retrieved')
            ->and($items)->toHaveCount(3)
            ->and($items->every(fn ($item) => $item->currency === $this->orders[0]->currency))->toBeTrue()
            ->and($items->map(fn ($item) => $item->status)->all())->toEqual([
                OrderStatusEnum::PENDING->value,
                OrderStatusEnum::SUCCESS->value,
                OrderStatusEnum::PENDING->value,
            ])->and($items->map(fn ($item) => $item->amount)->all())->toEqual([20000, 60000, 150000]);
    });
});

describe('get single order', function () {
    it('should thrown an exception if an order does not exist', function () {
        $response = $this->getJson('/api/orders/view/invalid_uuid');
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Invalid order');
    });

    it('should return a single order record', function () {
        $this->orders[0]->load('items', 'shipping', 'payment');

        $response = $this->getJson('/api/orders/view/'.$this->orders[0]->uuid);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Order retrieved')
            ->and($content->data->amount)->toBe($this->orders[0]->payment->order_amount)
            ->and($content->data->status)->toBe(OrderStatusEnum::PENDING->value)
            ->and($content->data->items)->toHaveCount(1);
    });
});
