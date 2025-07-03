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
        Schema::create('gateway_types', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('type');
            $table->string('category');
            $table->string('currency');
            $table->foreignId('primary_gateway_id')->constrained('gateways')->nullOnDelete();
            $table->foreignId('secondary_gateway_id')->nullable()->constrained('gateways')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['type', 'category', 'currency'], 'unique_gateway_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_types');
    }
};
