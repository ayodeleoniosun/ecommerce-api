<?php

namespace Database\Factories\Inventory;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Inventory\ProductImage;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
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
