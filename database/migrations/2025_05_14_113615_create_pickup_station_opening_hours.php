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
        Schema::create('pickup_station_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('pickup_station_id')->constrained('pickup_stations')->nullOnDelete();
            $table->string('day_of_week', 20);
            $table->time('opens_at');
            $table->time('closes_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_station_opening_hours');
    }
};
