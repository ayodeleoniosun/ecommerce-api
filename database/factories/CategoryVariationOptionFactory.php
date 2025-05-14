<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Inventory\CategoryVariation;
use App\Infrastructure\Models\Inventory\CategoryVariationOption;
use App\Infrastructure\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class CategoryVariationOptionFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = CategoryVariationOption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'variation_id' => CategoryVariation::factory()->create()->id,
            'value' => fake()->name,
        ];
    }
}
