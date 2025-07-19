<?php

namespace Database\Factories\Cart;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Enums\CartStatusEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCartItem>
 */
class UserCartItemFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = UserCartItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'cart_id' => UserCart::factory()->create()->id,
            'product_item_id' => ProductItem::factory()->create()->id,
            'quantity' => 5,
            'status' => CartStatusEnum::PENDING->value,
        ];
    }
}
