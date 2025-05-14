<?php

namespace Tests\Unit\Vendor\Products;

use App\Application\Shared\Enum\ProductStatusEnum;
use App\Domain\Vendor\Products\Actions\ViewVendorProduct;
use App\Domain\Vendor\Products\Resource\ViewProductResource;
use App\Infrastructure\Models\Inventory\Category;
use App\Infrastructure\Models\Inventory\CategoryVariationOption;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Vendor\Products\ProductRepository;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Mockery;

beforeEach(function () {
    $this->productRepo = Mockery::mock(ProductRepository::class)->makePartial();
    $this->viewProduct = new ViewVendorProduct($this->productRepo);
});

it('should return product details', function () {
    $vendor = User::factory()->create();
    $category = Category::factory()->create();
    $categoryVariationOption = CategoryVariationOption::factory()->create();

    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'category_id' => $category->id,
    ]);

    ProductItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['price' => 1000, 'quantity' => 10],
            ['price' => 2000, 'quantity' => 15],
            ['price' => 3000, 'quantity' => 20],
        ))
        ->create([
            'product_id' => $product->id,
            'variation_option_id' => $categoryVariationOption->id,
        ]);

    $response = $this->viewProduct->execute($product->uuid);

    expect($response)->toBeInstanceOf(ViewProductResource::class)
        ->and($response->resource->vendor_id)->toBe($product->vendor_id)
        ->and($response->resource->category_id)->toBe($product->category_id)
        ->and($response->resource->status)->toBe(ProductStatusEnum::IN_STOCK->value)
        ->and($response->resource->items->count())->toBe(3)
        ->and($response->resource->items->map(fn ($item) => $item->price)->all())->toEqual([1000, 2000, 3000])
        ->and($response->resource->items->map(fn ($item) => $item->quantity)->all())->toEqual([10, 15, 20]);
});
