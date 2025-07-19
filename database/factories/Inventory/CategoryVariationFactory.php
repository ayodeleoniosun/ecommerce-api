<?php

namespace Database\Factories\Inventory;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Inventory\Category;
use App\Infrastructure\Models\Inventory\CategoryVariation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryVariation>
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
