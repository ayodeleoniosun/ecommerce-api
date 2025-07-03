<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Constants\Currencies;
use App\Infrastructure\Models\Inventory\CategoryVariationOption;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductItem>
 */
class ProductItemFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = ProductItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'product_id' => Product::factory()->create()->id,
            'variation_option_id' => CategoryVariationOption::factory()->create()->id,
            'price' => 10000,
            'currency' => Currencies::NGN->value,
            'sku' => self::generateRandomCharacters(),
            'quantity' => 10,
        ];
    }
}
