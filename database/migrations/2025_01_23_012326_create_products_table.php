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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name', 150);
            $table->string('brand', 100)->nullable();
            $table->string('sku', 150)->nullable();
            $table->text('description')->nullable();
            $table->text('tags')->nullable();
            $table->string('material')->nullable();
            $table->bigInteger('price')->unsigned()->nullable();
            $table->text('short_description')->nullable();
            $table->string('slug')->nullable();
            $table->string('image')->nullable();
            $table->text('length')->nullable();
            $table->text('width')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('discount_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('discount_id')->references('id')->on('discounts');
            $table->timestamps();
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
