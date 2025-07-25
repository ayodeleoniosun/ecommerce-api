<?php

namespace Tests\Unit\Order\Cart;

use App\Domain\Order\Actions\Order\CheckoutAction;
use App\Domain\Order\Dtos\CheckoutDto;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Cart\UserCartRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderShippingRepositoryInterface;
use App\Domain\Order\Resources\Order\OrderResource;
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
use App\Infrastructure\Repositories\Order\OrderItemRepository;
use App\Infrastructure\Repositories\Shipping\PickupStation\PickupStationRepository;
use App\Infrastructure\Repositories\Shipping\ShippingAddress\CustomerShippingAddressRepository;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Mockery;

beforeEach(function () {
    $this->pickupStationRepo = Mockery::mock(PickupStationRepository::class)->makePartial();
    $this->customerShippingAddressRepo = Mockery::mock(CustomerShippingAddressRepository::class)->makePartial();
    $this->orderItemRepo = Mockery::mock(OrderItemRepository::class)->makePartial();
    $this->userCartRepo = Mockery::mock(UserCartRepositoryInterface::class);
    $this->orderRepo = Mockery::mock(OrderRepositoryInterface::class);
    $this->orderShippingRepo = Mockery::mock(OrderShippingRepositoryInterface::class);
    $this->orderPaymentRepo = Mockery::mock(OrderPaymentRepositoryInterface::class);

    $this->user = User::factory()->create();

    $this->customerShippingAddress = CustomerShippingAddress::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->pickupStation = PickupStation::factory()->create();

    $this->cart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->order = Order::factory()->create([
        'cart_id' => $this->cart->id,
        'user_id' => $this->user->id,
    ]);

    $this->product = Product::factory()->create();

    $this->productItem = ProductItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_id' => $this->product->id, 'price' => 10000, 'quantity' => 10],
            ['product_id' => $this->product->id, 'price' => 20000, 'quantity' => 15],
            ['product_id' => $this->product->id, 'price' => 30000, 'quantity' => 20],
        ))->create();

    $this->userCartItems = UserCartItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_item_id' => $this->productItem[0]->id, 'quantity' => 2],
            ['product_item_id' => $this->productItem[1]->id, 'quantity' => 3],
            ['product_item_id' => $this->productItem[2]->id, 'quantity' => 5],
        ))
        ->create([
            'cart_id' => $this->cart->id,
        ]);

    $this->orderItems = OrderItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['cart_item_id' => $this->userCartItems[0]->id, 'total_amount' => 20000],
            ['cart_item_id' => $this->userCartItems[1]->id, 'total_amount' => 60000],
            ['cart_item_id' => $this->userCartItems[2]->id, 'total_amount' => 150000],
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

    $this->checkoutDto = new CheckoutDto(
        $this->customerShippingAddress->uuid,
        $this->pickupStation->uuid,
        DeliveryTypeEnum::DOOR_DELIVERY->value,
        PaymentTypeEnum::CARD->value,
    );

    $this->checkout = new CheckoutAction(
        $this->pickupStationRepo,
        $this->customerShippingAddressRepo,
        $this->userCartRepo,
        $this->orderRepo,
        $this->orderShippingRepo,
        $this->orderItemRepo,
        $this->orderPaymentRepo
    );
});

it('should checkout cart items', function () {
    $this->userCartRepo->shouldReceive('findPendingCart')
        ->once()
        ->with($this->user->id)
        ->andReturn($this->cart);

    $this->orderRepo->shouldReceive('findOrCreate')
        ->once()
        ->andReturn($this->order->load('cart.items.productItem'));

    $this->orderShippingRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with([
            'order_id' => $this->order->id,
            'country_id' => $this->customerShippingAddress->country_id,
            'state_id' => $this->customerShippingAddress->state_id,
            'city_id' => $this->customerShippingAddress->city_id,
            'delivery_type' => DeliveryTypeEnum::DOOR_DELIVERY->value,
            'delivery_address' => $this->customerShippingAddress->address,
            'pickup_station_name' => $this->pickupStation->name,
            'pickup_station_address' => $this->pickupStation->address,
            'pickup_station_contact_name' => $this->pickupStation->contact_name,
            'pickup_station_contact_phone_number' => $this->pickupStation->contact_phone_number,
            'estimated_delivery_start_date' => now()->addDays(7)->toDateString(),
            'estimated_delivery_end_date' => now()->addDays(9)->toDateString(),
        ])
        ->andReturn($this->orderShipping);

    $this->orderPaymentRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with([
            'status' => OrderStatusEnum::PENDING->value,
            'order_id' => $this->order->id,
            'order_amount' => 230000,
            'currency' => $this->order->currency,
            'delivery_amount' => 1000,
        ])
        ->andReturn($this->orderPayment);

    $response = $this->checkout->execute($this->checkoutDto);

    expect($response)->toBeInstanceOf(OrderResource::class)
        ->and($response->reference)->toBe($this->order->reference)
        ->and($response->currency)->toBe($this->order->currency)
        ->and($response->cart_id)->toBe($this->cart->id)
        ->and($response->user_id)->toBe($this->user->id)
        ->and($response->items)->toHaveCount(3)
        ->and($response->items->every(fn ($item) => $item->order_id === $this->order->id))->toBeTrue()
        ->and($response->items->map(fn ($item) => $item->total_amount)->all())->toEqual([20000, 60000, 150000]);
});
