<?php

use App\Domain\Order\Enums\CartStatusEnum;
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
        Schema::create('guest_carts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('identifier');
            $table->string('status')->default(CartStatusEnum::PENDING->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_carts');
    }
};
