<?php

namespace Tests\Unit\Vendor\Products;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Vendor\Products\Actions\DeleteVendorProductItemAction;
use App\Infrastructure\Models\Inventory\Category;
use App\Infrastructure\Models\Inventory\CategoryVariationOption;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Vendor\Products\ProductItemRepository;
use Mockery;

beforeEach(function () {
    $this->productItemRepo = Mockery::mock(ProductItemRepository::class)->makePartial();
    $this->deleteProductItem = new DeleteVendorProductItemAction($this->productItemRepo);
});

it('should throw an error if product item is not found', function () {
    $this->deleteProductItem->execute('invalid_uuid');
})->throws(ResourceNotFoundException::class, 'Product item not found');

it('should delete product item', function () {
    $product = Product::factory()->create([
        'vendor_id' => User::factory()->create()->id,
        'category_id' => Category::factory()->create()->id,
    ]);

    $productItem = ProductItem::factory()->create([
        'product_id' => $product->id,
        'variation_option_id' => CategoryVariationOption::factory()->create()->id,
    ]);

    $response = $this->deleteProductItem->execute($productItem->uuid);

    expect($response)->toBeTrue();
});
