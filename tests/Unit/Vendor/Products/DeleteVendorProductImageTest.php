<?php

namespace Tests\Unit\Vendor\Products;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Vendor\Products\Actions\DeleteVendorProductImage;
use App\Infrastructure\Models\Inventory\ProductImage;
use App\Infrastructure\Repositories\Vendor\Products\ProductImageRepository;
use Mockery;

beforeEach(function () {
    $this->productImageRepo = Mockery::mock(ProductImageRepository::class)->makePartial();
    $this->deleteProductImage = new DeleteVendorProductImage($this->productImageRepo);
});

it('should throw an error if product image is not found', function () {
    $this->deleteProductImage->execute('invalid_uuid');
})->throws(ResourceNotFoundException::class, 'Product image not found');

it('should delete product image', function () {
    $productImage = ProductImage::factory()->create([
        'product_item_id' => 1,
    ]);

    $response = $this->deleteProductImage->execute($productImage->uuid);

    expect($response)->toBeTrue();
});
