<?php

namespace Database\Factories;

use App\Application\Shared\Enum\ProductEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'vendor_id' => User::factory()->create()->id,
            'category_id' => Category::factory()->create()->id,
            'name' => fake()->name,
            'description' => fake()->text,
            'status' => ProductEnum::IN_STOCK->value,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductEnum::IN_STOCK->value,
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductEnum::IN_STOCK->value,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductEnum::OUT_OF_STOCK->value,
        ]);
    }
}
