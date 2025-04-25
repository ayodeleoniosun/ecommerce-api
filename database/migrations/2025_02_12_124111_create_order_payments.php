<?php

use App\Application\Shared\Enum\PaymentEnum;
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
            $table->decimal('amount_paid', total: 10);
            $table->decimal('order_amount', total: 10);
            $table->decimal('fee');
            $table->decimal('vat');
            $table->string('processor')->nullable();
            $table->string('processor_reference')->nullable();
            $table->string('status')->default(PaymentEnum::PENDING->value);
            $table->text('narration')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
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
