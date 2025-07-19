<?php

namespace Tests\Feature\Order;

use App\Application\Shared\Enum\DeliveryTypeEnum;
use App\Application\Shared\Enum\OrderStatusEnum;
use App\Application\Shared\Enum\UserStatusEnum;
use App\Domain\Payment\Constants\PaymentTypeEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
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
        $userCart = UserCart::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $product = Product::factory()->create();

        $productItem = ProductItem::factory()
            ->count(3)
            ->state(new Sequence(
                ['product_id' => $product->id, 'price' => 10000, 'quantity' => 10],
                ['product_id' => $product->id, 'price' => 20000, 'quantity' => 15],
                ['product_id' => $product->id, 'price' => 30000, 'quantity' => 20],
            ))->create();

        UserCartItem::factory()
            ->count(3)
            ->state(new Sequence(
                ['product_item_id' => $productItem[0]->id, 'quantity' => 2],
                ['product_item_id' => $productItem[1]->id, 'quantity' => 3],
                ['product_item_id' => $productItem[2]->id, 'quantity' => 5],
            ))
            ->create([
                'cart_id' => $userCart->id,
            ]);

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
            ->and($content->data->amount)->toBe(230000)
            ->and($content->data->status)->toBe(OrderStatusEnum::PENDING->value);
    });
});
