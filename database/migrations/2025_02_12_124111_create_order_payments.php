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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('order_id')->constrained('orders')->nullOnDelete();
            $table->decimal('amount_paid', total: 10);
            $table->decimal('order_amount', total: 10);
            $table->decimal('fee');
            $table->decimal('vat');
            $table->string('processor')->nullable();
            $table->string('processor_reference')->nullable();
            $table->string('status')->default('pending');
            $table->text('narration')->nullable();
            $table->dateTime('completed_at');
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
