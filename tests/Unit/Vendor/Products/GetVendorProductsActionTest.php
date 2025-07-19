<?php

namespace Tests\Unit\Vendor\Products;

use App\Domain\Vendor\Products\Actions\GetVendorProductsAction;
use App\Domain\Vendor\Products\Enums\ProductStatusEnum;
use App\Domain\Vendor\Products\Resource\ProductResourceCollection;
use App\Infrastructure\Models\Inventory\Category;
use App\Infrastructure\Models\Inventory\CategoryVariationOption;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Vendor\Products\ProductRepository;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Request;
use Mockery;

beforeEach(function () {
    $this->productRepo = Mockery::mock(ProductRepository::class)->makePartial();
    $this->getProducts = new GetVendorProductsAction($this->productRepo);

    $this->vendor = User::factory()->create();
    $this->category = Category::factory()->create();
    $this->categoryVariationOption = CategoryVariationOption::factory()->create();

    $this->products = Product::factory()->count(3)
        ->create([
            'vendor_id' => $this->vendor->id,
            'category_id' => $this->category->id,
        ]);

    ProductItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_id' => $this->products[0], 'price' => 1000, 'quantity' => 10],
            ['product_id' => $this->products[1], 'price' => 2000, 'quantity' => 15],
            ['product_id' => $this->products[2], 'price' => 3000, 'quantity' => 20],
        ))
        ->create([
            'variation_option_id' => $this->categoryVariationOption->id,
        ]);

    $this->request = new Request([]);
});

it('should return all vendor products', function () {
    $response = $this->getProducts->execute($this->request);
    $items = collect($response->resource->items());

    expect($response)->toBeInstanceOf(ProductResourceCollection::class)
        ->and($items)->toHaveCount(3)
        ->and($items->every(fn ($item) => $item->vendor_id === $this->products[0]->vendor->id))->toBeTrue()
        ->and($items->every(fn ($item) => $item->category_id === $this->products[0]->category->id))->toBeTrue()
        ->and($items->every(fn ($item) => $item->status === ProductStatusEnum::IN_STOCK->value))->toBeTrue();
});
