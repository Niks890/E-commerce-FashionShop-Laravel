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
        Schema::create('vouche_usages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('voucher_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('voucher_id')->references('id')->on('vouchers');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouche_usages');
    }
};
