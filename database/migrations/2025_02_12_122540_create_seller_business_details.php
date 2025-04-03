<?php

use App\Application\Shared\Enum\UserEnum;
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
        Schema::create('seller_business_details', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->string('business_name');
            $table->text('business_description');
            $table->string('registration_number');
            $table->string('tax_identification_number');
            $table->string('business_certificate_path');
            $table->string('status')->default(UserEnum::PENDING->value);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_profile');
    }
};
