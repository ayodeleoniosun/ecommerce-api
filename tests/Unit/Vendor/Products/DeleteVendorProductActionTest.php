<?php

namespace Tests\Unit\Vendor\Products;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Vendor\Products\Actions\DeleteVendorProductAction;
use App\Infrastructure\Models\Inventory\Category;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Vendor\Products\ProductRepository;
use Mockery;

beforeEach(function () {
    $this->productRepo = Mockery::mock(ProductRepository::class)->makePartial();
    $this->deleteProduct = new DeleteVendorProductAction($this->productRepo);
});

it('should throw an error if product is not found', function () {
    $this->deleteProduct->execute('invalid_uuid');
})->throws(ResourceNotFoundException::class, 'Product not found');

it('should delete product', function () {
    $product = Product::factory()->create([
        'vendor_id' => User::factory()->create()->id,
        'category_id' => Category::factory()->create()->id,
    ]);

    $response = $this->deleteProduct->execute($product->uuid);

    expect($response)->toBeTrue();
});
