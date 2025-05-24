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
        Schema::create('order_shippings', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('order_id')->constrained('orders')->nullOnDelete();
            $table->foreignId('country_id')->constrained('countries')->nullOnDelete();
            $table->foreignId('state_id')->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->constrained('cities')->nullOnDelete();
            $table->string('delivery_type');
            $table->text('delivery_address');
            $table->text('pickup_station_name')->nullable();
            $table->text('pickup_station_address')->nullable();
            $table->text('pickup_station_contact_name')->nullable();
            $table->text('pickup_station_contact_phone_number')->nullable();
            $table->timestamp('estimated_delivery_start_date');
            $table->timestamp('estimated_delivery_end_date');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shippings');
    }
};
