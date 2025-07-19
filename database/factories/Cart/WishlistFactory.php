<?php

namespace Database\Factories\Cart;

use App\Infrastructure\Models\Cart\Wishlist;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Wishlist>
 */
class WishlistFactory extends Factory
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
        ];
    }
}
