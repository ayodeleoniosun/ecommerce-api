<?php

use App\Application\Shared\Enum\CartStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('cart_id')->constrained('user_carts')->nullOnDelete();
            $table->foreignId('product_item_id')->constrained('product_items')->nullOnDelete();
            $table->integer('quantity');
            $table->string('status')->default(CartStatusEnum::PENDING->value);
            $table->timestamp('reserved_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cart_items');
    }
};
