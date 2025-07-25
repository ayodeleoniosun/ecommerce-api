<?php

namespace Tests\Unit\Order\Wishlist;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Actions\Wishlist\AddToWishlistAction;
use App\Domain\Order\Dtos\WishlistDto;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Domain\Order\Resources\Cart\WishlistResource;
use App\Infrastructure\Models\Cart\Wishlist;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Vendor\Products\ProductItemRepository;
use Mockery;

beforeEach(function () {
    $this->productItemRepo = Mockery::mock(ProductItemRepository::class)->makePartial();
    $this->wishlistRepo = Mockery::mock(WishlistRepositoryInterface::class);

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

    $this->wishlistDto = new WishlistDto(
        productItemUUID: $this->productItem->uuid,
        productItemId: $this->productItem->id,
    );

    $this->actingAs($this->user, 'sanctum');

    $this->addToWishlist = new AddToWishlistAction($this->productItemRepo, $this->wishlistRepo);
});

it('should throw an exception if product item is not found', function () {
    $this->wishlistDto->setProductItemUUID('invalid-uuid');
    $this->addToWishlist->execute($this->wishlistDto);
})->throws(ResourceNotFoundException::class, 'Product item not found');

it('should throw an exception if product item is out of stock', function () {
    $this->productItem->quantity = 0;
    $this->productItem->save();

    $this->addToWishlist->execute($this->wishlistDto);
})->throws(BadRequestException::class, 'Product item is out of stock');

it('should throw an exception if wishlist item already exist', function () {
    $this->wishlistRepo->shouldReceive('findExistingWishlist')
        ->once()
        ->with($this->productItem->id)
        ->andReturn($this->wishlist);

    $this->addToWishlist->execute($this->wishlistDto);
})->throws(BadRequestException::class, 'Item already added to your wishlist');

it('should should add item to wishlist', function () {
    $this->wishlistRepo->shouldReceive('findExistingWishlist')
        ->once()
        ->with($this->productItem->id)
        ->andReturn(null);

    $this->wishlistRepo->shouldReceive('create')
        ->once()
        ->andReturn($this->wishlist);

    $response = $this->addToWishlist->execute($this->wishlistDto);

    expect($response)->toBeInstanceOf(WishlistResource::class)
        ->and($response->user_id)->toBe($this->user->id)
        ->and($response->product_item_id)->toBe($this->productItem->id);
});
