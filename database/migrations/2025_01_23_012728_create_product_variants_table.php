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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('color', 15)->nullable();
            $table->string('size', 10)->nullable();
            $table->decimal('price', 10,3)->nullable();
            $table->unsignedInteger('stock')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unique(['color', 'size', 'product_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
