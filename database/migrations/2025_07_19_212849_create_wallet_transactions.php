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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->nullOnDelete();
            $table->decimal('amount', total: 10);
            $table->decimal('amount_charged', total: 10);
            $table->string('type');
            $table->string('reference');
            $table->string('status')->default(PaymentStatusEnum::PENDING->value);
            $table->string('payment_method');
            $table->string('gateway');
            $table->string('gateway_reference');
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
        Schema::dropIfExists('wallet_transactions');
    }
};
