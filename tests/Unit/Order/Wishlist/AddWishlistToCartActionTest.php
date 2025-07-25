<?php

namespace Tests\Unit\Order\Wishlist;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Actions\Cart\AddToCartAction;
use App\Domain\Order\Actions\Wishlist\AddWishlistItemToCartAction;
use App\Domain\Order\Dtos\CartDto;
use App\Domain\Order\Enums\CartOperationEnum;
use App\Domain\Order\Enums\WishlistStatusEnum;
use App\Domain\Order\Resources\Cart\CartResource;
use App\Infrastructure\Models\Cart\Wishlist;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Cart\WishlistRepository;
use Mockery;

beforeEach(function () {
    $this->wishlistRepo = Mockery::mock(WishlistRepository::class)->makePartial();
    $this->addToCart = Mockery::mock(AddToCartAction::class);

    $this->user = User::factory()->create();
    $this->product = Product::factory()->create();

    $this->productItem = ProductItem::factory()->create([
        'product_id' => $this->product->id,
        'price' => 10000,
        'quantity' => 10,
    ]);

    $this->wishlist = Wishlist::factory()->create([
        'user_id' => $this->user->id,
        'product_item_id' => $this->productItem->id,
    ]);

    $this->actingAs($this->user, 'sanctum');

    $this->addToWishlistItemToCart = new AddWishlistItemToCartAction($this->wishlistRepo, $this->addToCart);
});
it('should throw an exception if wishlist item is not found', function () {
    $this->addToWishlistItemToCart->execute('invalid-uuid');
})->throws(ResourceNotFoundException::class, 'Item not found in your wishlist');

it('should throw an exception if wishlist item has already been added to cart', function () {
    $this->wishlist->status = WishlistStatusEnum::ADDED_TO_CART->value;
    $this->wishlist->save();

    $this->addToWishlistItemToCart->execute($this->wishlist->uuid);
})->throws(ResourceNotFoundException::class, 'Item not found in your wishlist');

it('should should add wishlist item to cart', function () {
    $mockedCartResource = Mockery::mock(CartResource::class);

    $this->addToCart
        ->shouldReceive('execute')
        ->once()
        ->withArgs(function (CartDto $dto) {
            return $dto->getProductItemId() === $this->productItem->id
                && $dto->getProductItemUUID() === $this->productItem->uuid
                && $dto->getCurrency() === $this->productItem->currency
                && $dto->getQuantity() === 1
                && $dto->getType() === CartOperationEnum::INCREMENT->value;
        })
        ->andReturn($mockedCartResource);

    $this->addToWishlistItemToCart->execute($this->wishlist->uuid);
});
