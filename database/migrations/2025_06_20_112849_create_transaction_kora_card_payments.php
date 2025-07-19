<?php

use App\Domain\Payment\Enums\PaymentStatusEnum;
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
        Schema::create('transaction_kora_card_payments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('order_payment_id')->constrained('order_payments')->nullOnDelete();
            $table->string('reference');
            $table->string('currency');
            $table->string('auth_model')->nullable();
            $table->decimal('amount', total: 10);
            $table->string('status')->default(PaymentStatusEnum::PENDING->value);
            $table->string('gateway_response')->nullable();
            $table->string('gateway_response_code')->nullable();
            $table->string('gateway_transaction_reference')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_kora_card_payments');
    }
};
