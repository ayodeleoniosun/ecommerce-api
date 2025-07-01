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
        Schema::create('api_logs_kora_card_payments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('transaction_id')->constrained('transaction_kora_card_payments')->nullOnDelete();
            $table->json('charge_response');
            $table->json('verify_response');
            $table->json('webhook_request');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_shipping_addresses');
    }
};
