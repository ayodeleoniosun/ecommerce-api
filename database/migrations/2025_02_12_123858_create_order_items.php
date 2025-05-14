<?php

use App\Application\Shared\Enum\ProductStatusEnum;
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('order_id')->constrained('orders')->nullOnDelete();
            $table->foreignId('product_item_id')->constrained('product_items')->nullOnDelete();
            $table->integer('quantity');
            $table->decimal('total_amount', 10);
            $table->string('status')->default(ProductStatusEnum::IN_STOCK->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
