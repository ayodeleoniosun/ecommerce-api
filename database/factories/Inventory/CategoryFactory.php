<?php

namespace Database\Factories\Inventory;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Inventory\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'name' => fake()->name,
            'slug' => fake()->name,
        ];
    }
}
