<?php

namespace Tests\Unit\Order;

use App\Application\Shared\Enum\CartOperationEnum;
use App\Application\Shared\Enum\ProductStatusEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Actions\Cart\AddToCart;
use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use App\Domain\Order\Resources\Cart\CartResource;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Vendor\Products\ProductItemRepository;
use Mockery;

beforeEach(function () {
    $this->productItemRepo = Mockery::mock(ProductItemRepository::class)->makePartial();
    $this->userCartRepo = Mockery::mock(UserCartRepositoryInterface::class);
    $this->userCartItemRepo = Mockery::mock(UserCartItemRepositoryInterface::class);

    $this->user = User::factory()->create();

    $this->userCart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->productItem = ProductItem::factory()->create([
        'quantity' => 10,
    ]);

    $this->userCartItem = UserCartItem::factory()->create([
        'cart_id' => $this->userCart->id,
        'product_item_id' => $this->productItem->id,
    ]);

    $this->addCartDto = new AddToCartDto(
        $this->productItem->uuid,
        $this->productItem->id,
        5,
        CartOperationEnum::INCREMENT->value,
        $this->user->id
    );

    $this->addToCart = new AddToCart($this->productItemRepo, $this->userCartRepo, $this->userCartItemRepo);
});

it('should throw an exception if product item does not exist', function () {
    $this->addCartDto->setProductItemUUID('invalid_uuid');
    $this->addToCart->execute($this->addCartDto);
})->throws(ResourceNotFoundException::class, 'Product item not found');

it('should throw an exception if product item quantity is lesser than the cart quantity', function () {
    $this->addCartDto->setQuantity(100);
    $this->addToCart->execute($this->addCartDto);
})->throws(BadRequestException::class, 'Insufficient product quantity');

it('should add new items to cart', function () {
    $this->userCartRepo->shouldReceive('findPendingCart')
        ->once()
        ->with($this->addCartDto->getUserId())
        ->andReturn($this->userCart);

    $this->userCartItemRepo->shouldReceive('findExistingCartItem')
        ->once()
        ->with($this->userCart->id, $this->addCartDto->getProductItemId())
        ->andReturn(null);

    $this->userCartRepo->shouldReceive('findOrCreate')
        ->once()
        ->with($this->addCartDto)
        ->andReturn($this->userCart);

    $this->userCartItemRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with($this->addCartDto)
        ->andReturn($this->userCartItem);

    $response = $this->addToCart->execute($this->addCartDto);

    expect($response)->toBeInstanceOf(CartResource::class)
        ->and($response->resource->quantity)->toBe($this->addCartDto->getQuantity())
        ->and($response->resource->productItem->price)->toBe(10000)
        ->and($response->resource->productItem->quantity)->toBe(5)
        ->and($response->resource->productItem->status)->toBe(ProductStatusEnum::IN_STOCK->value);
});

it('should increment existing cart item', function () {
    $this->userCartRepo->shouldReceive('findPendingCart')
        ->once()
        ->with($this->addCartDto->getUserId())
        ->andReturn($this->userCart);

    $this->userCartItemRepo->shouldReceive('findExistingCartItem')
        ->once()
        ->with($this->userCart->id, $this->addCartDto->getProductItemId())
        ->andReturn($this->userCartItem);

    $this->userCartRepo->shouldReceive('findOrCreate')
        ->once()
        ->with($this->addCartDto)
        ->andReturn($this->userCart);

    $this->userCartItem->quantity = 10;
    $this->userCartItem->save();

    $this->userCartItemRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with($this->addCartDto)
        ->andReturn($this->userCartItem);

    $this->productItem->quantity = 5;
    $this->productItem->save();

    $response = $this->addToCart->execute($this->addCartDto);

    expect($response)->toBeInstanceOf(CartResource::class)
        ->and($response->resource->quantity)->toBe(10)
        ->and($response->resource->productItem->price)->toBe(10000)
        ->and($response->resource->productItem->quantity)->toBe(0)
        ->and($response->resource->productItem->status)->toBe(ProductStatusEnum::IN_STOCK->value);
});

it('should decrement existing cart item', function () {
    $this->addCartDto->setType(CartOperationEnum::DECREMENT->value);

    $this->userCartRepo->shouldReceive('findPendingCart')
        ->once()
        ->with($this->addCartDto->getUserId())
        ->andReturn($this->userCart);

    $this->userCartItemRepo->shouldReceive('findExistingCartItem')
        ->once()
        ->with($this->userCart->id, $this->addCartDto->getProductItemId())
        ->andReturn($this->userCartItem);

    $this->userCartRepo->shouldReceive('findOrCreate')
        ->once()
        ->with($this->addCartDto)
        ->andReturn($this->userCart);

    $this->userCartItem->quantity = 2;
    $this->userCartItem->save();

    $this->userCartItemRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with($this->addCartDto)
        ->andReturn($this->userCartItem);

    $response = $this->addToCart->execute($this->addCartDto);

    expect($response)->toBeInstanceOf(CartResource::class)
        ->and($response->resource->quantity)->toBe(2)
        ->and($response->resource->productItem->price)->toBe(10000)
        ->and($response->resource->productItem->quantity)->toBe(5)
        ->and($response->resource->productItem->status)->toBe(ProductStatusEnum::IN_STOCK->value);
});
