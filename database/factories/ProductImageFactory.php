<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\ProductImage;
use App\Infrastructure\Models\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductImageFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = ProductImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'product_item_id' => ProductItem::factory()->create()->id,
            'path' => 'vendors/products/image/fake.jpg',
        ];
    }
}
