<?php

namespace App\Domain\Order\Commands;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Infrastructure\Models\Cart\UserCartItem;
use Illuminate\Console\Command;

class RestoreAbandonedCartQuantity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:restore-abandoned-cart-quantity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command restores cart items that have been reserved for over 20 minutes and replenishes the corresponding product item stock.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UserCartItem::where('reserved_until', '<', now())
            ->chunk(100, function ($cartItems) {
                foreach ($cartItems as $cartItem) {
                    $cartItem->productItem->increment('quantity', $cartItem->quantity);

                    $cartItem->update([
                        'status' => CartStatusEnum::STOCK_RESTORED->value,
                    ]);
                }
            });
    }
}
