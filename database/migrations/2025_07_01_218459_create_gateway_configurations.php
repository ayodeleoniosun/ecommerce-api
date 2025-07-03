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
        Schema::create('gateway_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('type');
            $table->string('category');
            $table->string('currency');
            $table->foreignId('gateway_id')->constrained('gateways')->nullOnDelete();
            $table->string('enabled')->default(true);
            $table->json('settings');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['type', 'category', 'currency', 'gateway_id'], 'unique_gateway_configuration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_configurations');
    }
};
