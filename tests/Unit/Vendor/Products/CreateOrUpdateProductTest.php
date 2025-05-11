<?php

namespace Tests\Unit\Vendor\Products;

use App\Application\Shared\Enum\ProductEnum;
use App\Domain\Vendor\Products\Actions\CreateOrUpdateProduct;
use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\AllProductResource;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\User;
use Mockery;

beforeEach(function () {
    $this->productRepo = Mockery::mock(ProductRepositoryInterface::class);
    $this->vendor = User::factory()->create();
    $this->category = Category::factory()->create();

    $this->productDto = new CreateOrUpdateProductDto(
        $this->category->uuid,
        $this->vendor->id,
        $this->category->id,
        'Black shoe',
        'This is a black shoe'
    );

    $this->product = new Product([
        'vendor_id' => $this->productDto->getVendorId(),
        'category_id' => $this->productDto->getCategoryId(),
        'name' => $this->productDto->getName(),
        'description' => $this->productDto->getDescription(),
        'status' => ProductEnum::IN_STOCK->value,
    ]);

    $this->createOrUpdateProduct = new CreateOrUpdateProduct($this->productRepo);
});

it('should create a new vendor product', function () {
    $this->productRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with($this->productDto)
        ->andReturn($this->product);

    $response = $this->createOrUpdateProduct->execute($this->productDto);

    expect($response['is_existing_product'])->toBeFalse()
        ->and($response['product'])->toBeInstanceOf(AllProductResource::class)
        ->and($response['product']->resource->vendor_id)->toBe($this->productDto->getVendorId())
        ->and($response['product']->resource->category_id)->toBe($this->productDto->getCategoryId())
        ->and($response['product']->resource->name)->toBe($this->productDto->getName())
        ->and($response['product']->resource->description)->toBe($this->productDto->getDescription())
        ->and($response['product']->resource->status)->toBe(ProductEnum::IN_STOCK->value);
});

it('should update an existing vendor product', function () {
    $this->productDto->setProductId(1);

    $this->productRepo->shouldReceive('storeOrUpdate')
        ->once()
        ->with($this->productDto)
        ->andReturn($this->product);

    $response = $this->createOrUpdateProduct->execute($this->productDto);

    expect($response['is_existing_product'])->toBeTrue()
        ->and($response['product'])->toBeInstanceOf(AllProductResource::class)
        ->and($response['product']->resource->vendor_id)->toBe($this->productDto->getVendorId())
        ->and($response['product']->resource->category_id)->toBe($this->productDto->getCategoryId())
        ->and($response['product']->resource->name)->toBe($this->productDto->getName())
        ->and($response['product']->resource->description)->toBe($this->productDto->getDescription())
        ->and($response['product']->resource->status)->toBe(ProductEnum::IN_STOCK->value);
});
