<?php

namespace Database\Factories;

use App\Application\Shared\Enum\ProductStatusEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Inventory\Category;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\User\User;
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
            'status' => ProductStatusEnum::IN_STOCK->value,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatusEnum::IN_STOCK->value,
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatusEnum::IN_STOCK->value,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatusEnum::OUT_OF_STOCK->value,
        ]);
    }
}
