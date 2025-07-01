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
            $table->string('currency', 3);
            $table->decimal('order_amount', total: 10);
            $table->decimal('delivery_amount', total: 10);
            $table->decimal('amount_charged', total: 10)->nullable();
            $table->string('status')->default(PaymentStatusEnum::PENDING->value);
            $table->string('payment_method')->nullable();
            $table->text('narration')->nullable();
            $table->string('processor')->nullable();
            $table->string('processor_reference')->nullable();
            $table->decimal('fee')->nullable();
            $table->decimal('vat')->nullable();
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
