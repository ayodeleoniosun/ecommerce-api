<?php

namespace Database\Factories;

use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Infrastructure\Models\Cart\UserCart>
 */
class CartFactory extends Factory
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
            'product_id' => Product::factory()->create()->id,
            'quantity' => 2,
        ];
    }
}
