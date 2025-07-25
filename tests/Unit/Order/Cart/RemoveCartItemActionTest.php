<?php

namespace Tests\Unit\Order\Cart;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Actions\Cart\RemoveCartItemAction;
use App\Domain\Order\Enums\CartStatusEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Cart\UserCartItemRepository;
use Mockery;

beforeEach(function () {
    $this->userCartItemRepo = Mockery::mock(UserCartItemRepository::class)->makePartial();
    $this->user = User::factory()->create();

    $this->userCart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->product = Product::factory()->create();

    $this->productItem = ProductItem::factory()->create([
        'product_id' => Product::factory()->create()->id,
    ]);

    $this->userCartItem = UserCartItem::factory()->create([
        'cart_id' => $this->userCart->id,
        'product_item_id' => $this->productItem->id,
    ]);

    $this->actingAs($this->user, 'sanctum');

    $this->deleteCartItem = new RemoveCartItemAction($this->userCartItemRepo);
});

it('should throw an exception if cart item does not exist', function () {
    $this->deleteCartItem->execute('invalid_uuid');
})->throws(ResourceNotFoundException::class, 'Item not found in your cart');

it('should throw an exception if cart item has already been checked out', function () {
    $this->userCartItem->status = CartStatusEnum::CHECKED_OUT->value;
    $this->userCartItem->save();

    $this->deleteCartItem->execute($this->userCartItem->uuid);
})->throws(ResourceNotFoundException::class, 'Item not found in your cart');

it('should delete an existing cart item', function () {
    $response = $this->deleteCartItem->execute($this->userCartItem->uuid);

    expect($response)->toBeTrue();
});
