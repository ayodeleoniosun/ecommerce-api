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
        Schema::create('pickup_stations', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('country_id')->constrained('countries')->nullOnDelete();
            $table->foreignId('state_id')->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->constrained('cities')->nullOnDelete();
            $table->string('name');
            $table->text('address');
            $table->string('contact_phone_number');
            $table->string('contact_name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_stations');
    }
};
