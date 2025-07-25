<?php

namespace Database\Factories\Cart;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Cart\Wishlist;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wishlist>
 */
class WishlistFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = Wishlist::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'user_id' => User::factory()->create()->id,
            'product_item_id' => ProductItem::factory()->create()->id,
        ];
    }
}
