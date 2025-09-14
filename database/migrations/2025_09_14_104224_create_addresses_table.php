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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id');
            $table->string('province_code')->nullable();
            $table->string('province_name')->nullable();
            $table->string('ward_code')->nullable();
            $table->string('ward_name')->nullable();
            $table->string('street_address');
            $table->text('full_address');
            $table->boolean('is_default')->default(false); // Địa chỉ mặc định
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
