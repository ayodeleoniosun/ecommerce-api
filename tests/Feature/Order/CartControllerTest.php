<?php

namespace Tests\Feature\Order;

use App\Application\Shared\Enum\CartOperationEnum;
use App\Application\Shared\Enum\ProductStatusEnum;
use App\Application\Shared\Enum\UserStatusEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Response;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
        'status' => UserStatusEnum::ACTIVE->value,
    ]);

    $this->productItem = ProductItem::factory()->create([
        'product_id' => Product::factory()->create()->id,
    ]);

    $this->userCart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->actingAs($this->user, 'sanctum');
});

describe('add item to cart', function () {
    it('should return an error if a required field is empty', function () {
        $payload = [
            'merged_product_item_id' => 'John Doe',
        ];

        $response = $this->postJson('/api/customers/carts', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The product item id field is required.');
    });

    it('should return an error if type is not valid', function () {
        $payload = [
            'product_item_id' => $this->productItem->uuid,
            'merged_product_item_id' => $this->productItem->id,
            'quantity' => 10,
            'type' => 'invalid_type',
        ];

        $response = $this->postJson('/api/customers/carts', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The selected type is invalid.');
    });

    it('should add item to cart', function () {
        $payload = [
            'product_item_id' => $this->productItem->uuid,
            'merged_product_item_id' => $this->productItem->id,
            'quantity' => 7,
            'type' => CartOperationEnum::INCREMENT->value,
        ];

        $response = $this->postJson('/api/customers/carts', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_CREATED);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Item successfully added to cart')
            ->and($content->data->product_item_id)->toBe($payload['product_item_id'])
            ->and($content->data->cart_quantity)->toBe($payload['quantity'])
            ->and($content->data->remaining_quantity)->toBe(3)
            ->and($content->data->status)->toBe(ProductStatusEnum::IN_STOCK->value);
    });
});

describe('remove cart item', function () {
    it('should return an error if cart item is invalid', function () {
        $invalidItemUUID = 'invalid-uuid';

        $response = $this->deleteJson('/api/customers/carts/'.$invalidItemUUID);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Item not found in cart');
    });

    it('should return an error if type is not valid', function () {
        $userCartItem = UserCartItem::factory()->create([
            'cart_id' => $this->userCart->id,
            'product_item_id' => $this->productItem->id,
        ]);

        $response = $this->deleteJson('/api/customers/carts/'.$userCartItem->uuid);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Item successfully removed from cart');
    })->group('test');
});

describe('get all cart items', function () {
    it('should get all cart items', function () {
        $product = Product::factory()->create();

        $userCart = UserCart::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $productItems = ProductItem::factory()
            ->count(3)
            ->state(new Sequence(
                ['product_id' => $product->id, 'price' => 10000, 'quantity' => 10],
                ['product_id' => $product->id, 'price' => 20000, 'quantity' => 15],
                ['product_id' => $product->id, 'price' => 30000, 'quantity' => 20],
            ))->create();

        UserCartItem::factory()
            ->count(3)
            ->state(new Sequence(
                ['product_item_id' => $productItems[0]->id, 'quantity' => 2],
                ['product_item_id' => $productItems[1]->id, 'quantity' => 3],
                ['product_item_id' => $productItems[2]->id, 'quantity' => 5],
            ))
            ->create([
                'cart_id' => $userCart->id,
            ]);

        $response = $this->getJson('/api/customers/carts');
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_OK);

        $items = collect($content->data);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Cart items retrieved')
            ->and($content->data)->toHaveCount(3)
            ->and($items->every(fn ($item) => $item->status === ProductStatusEnum::IN_STOCK->value))->toBeTrue()
            ->and($items->map(fn ($item) => $item->cart_quantity)->all())->toEqual([2, 3, 5])
            ->and($items->map(fn ($item) => $item->remaining_quantity)->all())->toEqual([10, 15, 20]);
    });
});
