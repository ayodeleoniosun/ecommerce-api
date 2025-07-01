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
        Schema::create('transaction_kora_card_payments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('order_payment_id')->constrained('order_payments')->nullOnDelete();
            $table->string('reference');
            $table->string('currency');
            $table->decimal('amount', total: 10);
            $table->string('status')->default(PaymentStatusEnum::PENDING->value);
            $table->string('processor_response')->nullable();
            $table->string('processor_response_code')->nullable();
            $table->string('processor_transaction_id')->nullable();
            $table->json('meta');
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
