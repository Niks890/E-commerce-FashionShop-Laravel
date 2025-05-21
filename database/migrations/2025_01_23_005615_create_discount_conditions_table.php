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
        Schema::create('discount_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discount_conditions_min_quantity')->unsigned()->nullable();
            $table->decimal('discount_conditions_percent', 10,2)->nullable();
            $table->integer('discount_gift_product_quantity')->unsigned()->nullable();
            $table->unsignedInteger('discount_id');
            $table->foreign('discount_id')->references('id')->on('discounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_conditions');
    }
};
