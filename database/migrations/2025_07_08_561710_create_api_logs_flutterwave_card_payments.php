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
        Schema::create('api_logs_flutterwave_card_payments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('transaction_id')->constrained('transaction_flutterwave_card_payments')->nullOnDelete();
            $table->json('charge_response')->nullable();
            $table->json('verify_response')->nullable();
            $table->json('webhook_request')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs_flutterwave_card_payments');
    }
};
