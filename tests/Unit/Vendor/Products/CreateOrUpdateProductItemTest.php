<?php

namespace Tests\Unit\Vendor\Products;

use App\Application\Shared\Enum\ProductStatusEnum;
use App\Domain\Payment\Constants\Currencies;
use App\Domain\Vendor\Products\Actions\CreateOrUpdateProductItem;
use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Domain\Vendor\Products\Resource\ProductItemResource;
use App\Infrastructure\Models\Inventory\CategoryVariationOption;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Repositories\Vendor\Products\ProductItemRepository;
use Mockery;

beforeEach(function () {
    $this->productItemRepo = Mockery::mock(ProductItemRepository::class)->makePartial();
    $this->categoryVariationOption = CategoryVariationOption::factory()->create();
    $this->product = Product::factory()->create();

    $this->productItemDto = new CreateOrUpdateProductItemDto(
        $this->product->uuid,
        $this->categoryVariationOption->uuid,
        $this->product->id,
        $this->categoryVariationOption->id,
        10000,
        Currencies::NGN->value,
        20
    );

    $this->productItem = new ProductItem([
        'id' => 1,
        'product_id' => $this->product->id,
        'variation_option_id' => $this->productItemDto->getCategoryVariationOptionId(),
        'price' => $this->productItemDto->getPrice(),
        'quantity' => $this->productItemDto->getQuantity(),
        'currency' => $this->productItemDto->getCurrency(),
        'status' => ProductStatusEnum::IN_STOCK->value,
    ]);

    $this->createOrUpdateProductItem = new CreateOrUpdateProductItem($this->productItemRepo);
});

it('should create a new vendor product item', function () {
    $this->productItemRepo->shouldReceive('findExistingProductItem')
        ->once()
        ->with($this->productItemDto->getProductId(), $this->productItemDto->getCategoryVariationOptionId())
        ->andReturn(null);

    $this->productItemRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with($this->productItemDto)
        ->andReturn($this->productItem);

    $response = $this->createOrUpdateProductItem->execute($this->productItemDto);

    expect($response['is_existing_product_item'])->toBeFalse()
        ->and($response['product_item'])->toBeInstanceOf(ProductItemResource::class)
        ->and($response['product_item']->resource->product_id)->toBe($this->productItemDto->getProductId())
        ->and($response['product_item']->resource->variation_option_id)->toBe($this->productItemDto->getCategoryVariationOptionId())
        ->and($response['product_item']->resource->price)->toBe($this->productItemDto->getPrice())
        ->and($response['product_item']->resource->quantity)->toBe($this->productItemDto->getQuantity())
        ->and($response['product_item']->resource->status)->toBe(ProductStatusEnum::IN_STOCK->value);
});

it('should update an existing vendor product', function () {
    $productItem = ProductItem::factory()->create($this->productItem->toArray());

    $this->productItemRepo->shouldReceive('findExistingProductItem')
        ->once()
        ->with($this->productItemDto->getProductId(), $this->productItemDto->getCategoryVariationOptionId())
        ->andReturn($productItem);

    $this->productItemRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with($this->productItemDto)
        ->andReturn($productItem);

    $response = $this->createOrUpdateProductItem->execute($this->productItemDto);

    expect($response['is_existing_product_item'])->toBeTrue()
        ->and($response['product_item'])->toBeInstanceOf(ProductItemResource::class)
        ->and($response['product_item']->resource->product_id)->toBe($this->productItemDto->getProductId())
        ->and($response['product_item']->resource->variation_option_id)->toBe($this->productItemDto->getCategoryVariationOptionId())
        ->and($response['product_item']->resource->price)->toBe($this->productItemDto->getPrice())
        ->and($response['product_item']->resource->quantity)->toBe($this->productItemDto->getQuantity())
        ->and($response['product_item']->resource->status)->toBe(ProductStatusEnum::IN_STOCK->value);
});
