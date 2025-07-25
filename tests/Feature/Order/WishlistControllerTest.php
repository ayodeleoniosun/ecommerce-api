<?php

namespace Tests\Feature\Order;

use App\Domain\Auth\Enums\UserStatusEnum;
use App\Domain\Order\Enums\WishlistStatusEnum;
use App\Infrastructure\Models\Cart\Wishlist;
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

    $this->actingAs($this->user, 'sanctum');
});

describe('add product item to wishlist', function () {
    it('should return an error if product item id is invalid', function () {
        $payload = [
            'product_item_id' => 'invalid_uuid',
        ];

        $response = $this->postJson('/api/wishlists', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The selected product item id is invalid.');
    });

    it('should return an error if product item is out of stock', function () {
        $this->productItems[0]->quantity = 0;
        $this->productItems[0]->save();

        $payload = [
            'product_item_id' => $this->productItems[0]->uuid,
        ];

        $response = $this->postJson('/api/wishlists', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Product item is out of stock');
    });

    it('should return an error if product item already exist in wishlist', function () {
        Wishlist::factory()->create([
            'user_id' => $this->user->id,
            'product_item_id' => $this->productItems[0]->id,
        ]);

        $payload = [
            'product_item_id' => $this->productItems[0]->uuid,
        ];

        $response = $this->postJson('/api/wishlists', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Item already added to your wishlist');
    });

    it('should successfully add product item to wishlist', function () {
        $payload = [
            'product_item_id' => $this->productItems[0]->uuid,
        ];

        $response = $this->postJson('/api/wishlists', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_CREATED);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Item successfully added to your wishlist')
            ->and($content->data->unit_price)->toBe(number_format($this->productItems[0]->price, 2));
    });
});

describe('add wishlist item to cart', function () {
    it('should return an error if wishlist item does not exist', function () {
        $response = $this->postJson('/api/wishlists/invalid_uuid/cart');
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Item not found in your wishlist');
    });

    it('should return an error if wishlist item has already been added to cart', function () {
        $wishlist = Wishlist::factory()->create([
            'user_id' => $this->user->id,
            'product_item_id' => $this->productItems[0]->id,
            'status' => WishlistStatusEnum::ADDED_TO_CART->value,
        ]);

        $response = $this->postJson('/api/wishlists/'.$wishlist->uuid.'/cart');
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Item not found in your wishlist');
    });

    it('should add wishlist item to cart', function () {
        $wishlist = Wishlist::factory()->create([
            'user_id' => $this->user->id,
            'product_item_id' => $this->productItems[0]->id,
        ]);

        $response = $this->postJson('/api/wishlists/'.$wishlist->uuid.'/cart');
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_CREATED);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Item successfully added to your cart')
            ->and($content->data->unit_price)->toBe(number_format($this->productItems[0]->price, 2))
            ->and($content->data->cart_quantity)->toBe(1)
            ->and($content->data->remaining_quantity)->toBe($this->productItems[0]->quantity - 1);
    });
});

describe('remove item from wishlist', function () {
    it('should return an error if wishlist item does not exist', function () {
        $response = $this->deleteJson('/api/wishlists/invalid_uuid');
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Item not found in your wishlist');
    });

    it('should return an error if wishlist item has already been added to cart', function () {
        $wishlist = Wishlist::factory()->create([
            'user_id' => $this->user->id,
            'product_item_id' => $this->productItems[0]->id,
            'status' => WishlistStatusEnum::ADDED_TO_CART->value,
        ]);

        $response = $this->deleteJson('/api/wishlists/invalid_uuid');
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Item not found in your wishlist');
    });

    it('should remove item from wishlist', function () {
        $wishlist = Wishlist::factory()->create([
            'user_id' => $this->user->id,
            'product_item_id' => $this->productItems[0]->id,
        ]);

        $response = $this->deleteJson('/api/wishlists/'.$wishlist->uuid);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Item successfully removed from your wishlist');
    });
});

describe('get wishlist items', function () {
    it('should return wishlist items', function () {
        Wishlist::factory()
            ->count(3)
            ->state(new Sequence(
                ['product_item_id' => $this->productItems[0]->id],
                ['product_item_id' => $this->productItems[1]->id],
                ['product_item_id' => $this->productItems[2]->id],
            ))->create([
                'user_id' => $this->user->id,
            ]);

        $response = $this->getJson('/api/wishlists');
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Wishlist items retrieved')
            ->and($content->data->items)->toHaveCount(3);
    });
});
