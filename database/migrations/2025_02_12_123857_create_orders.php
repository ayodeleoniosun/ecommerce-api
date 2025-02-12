<?php

use App\Domains\Common\Enum\PaymentEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->nullOnDelete();
            $table->string('quantity');
            $table->string('status')->default(PaymentEnum::PENDING->value);
            $table->timestamps();
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
