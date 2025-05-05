<?php

use App\Application\Shared\Enum\ProductEnum;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('vendor_id')->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->text('description');
            $table->string('status')->default(ProductEnum::ACTIVE->value);
            $table->timestamp('disabled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
