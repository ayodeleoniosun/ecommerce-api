<?php

use App\Application\Shared\Enum\PaymentStatusEnum;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('cart_item_id')->constrained('user_cart_items')->nullOnDelete();
            $table->text('shipping_address');
            $table->text('shipping_additional_note');
            $table->text('state');
            $table->text('city');
            $table->string('status')->default(PaymentStatusEnum::PENDING->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
