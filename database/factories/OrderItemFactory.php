<?php

namespace Database\Factories;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'order_id' => Order::factory()->create()->id,
            'product_item_id' => ProductItem::factory()->create()->id,
            'quantity' => 5,
            'total_amount' => 20000,
        ];
    }
}
