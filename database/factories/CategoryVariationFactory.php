<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\CategoryVariation;
use App\Infrastructure\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class CategoryVariationFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = CategoryVariation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'category_id' => Category::factory()->create()->id,
            'name' => fake()->name,
        ];
    }
}
