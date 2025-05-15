<?php

use App\Application\Shared\Enum\AddressTypeEnum;
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
        Schema::create('customer_shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phone_number');
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('country_id')->constrained('countries')->nullOnDelete();
            $table->foreignId('state_id')->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->constrained('cities')->nullOnDelete();
            $table->text('address');
            $table->string('status')->default(AddressTypeEnum::OTHERS->value);
            $table->text('additional_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_shipping_addresses');
    }
};
