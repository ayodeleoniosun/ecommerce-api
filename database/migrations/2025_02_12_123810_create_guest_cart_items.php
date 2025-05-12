<?php

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
        Schema::create('guest_cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('guest_cart_id')->constrained('guest_carts')->nullOnDelete();
            $table->foreignId('product_item_id')->constrained('product_items')->nullOnDelete();
            $table->integer('quantity');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_cart_items');
    }
};
