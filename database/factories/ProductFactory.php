<?php

namespace Database\Factories;

use App\Application\Shared\Enum\ProductEnum;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => str::uuid(),
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::factory()->create()->id,
            'name' => fake()->name,
            'quantity' => 10,
            'price' => 1000,
            'description' => fake()->text,
            'sku' => 10,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductEnum::ACTIVE->value,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductEnum::OUT_OF_STOCK->value,
        ]);
    }
}
