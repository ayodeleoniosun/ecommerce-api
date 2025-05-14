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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('order_id')->constrained('orders')->nullOnDelete();
            $table->string('reference');
            $table->decimal('order_amount', total: 10);
            $table->decimal('delivery_amount', total: 10);
            $table->decimal('total_amount', total: 10);
            $table->decimal('amount_paid', total: 10);
            $table->string('status')->default(PaymentStatusEnum::PENDING->value);
            $table->string('payment_method')->nullable();
            $table->text('narration')->nullable();
            $table->string('processor')->nullable();
            $table->string('processor_reference')->nullable();
            $table->decimal('transaction_fee')->nullable();
            $table->decimal('transaction_vat')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
