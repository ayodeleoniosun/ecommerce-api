<?php

use App\Application\Shared\Enum\ProductEnum;
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
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->nullOnDelete();
            $table->foreignId('variation_option_id')->constrained('category_variation_options')->nullOnDelete();
            $table->string('uuid')->unique();
            $table->decimal('price', total: 10);
            $table->integer('quantity');
            $table->string('sku');
            $table->string('status')->default(ProductEnum::IN_STOCK->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
