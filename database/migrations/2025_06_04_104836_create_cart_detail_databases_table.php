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
        Schema::create('cart_detail_databases', function (Blueprint $table) {
            $table->id(); // Add auto-increment primary key
            $table->unsignedBigInteger('cart_id');
            $table->foreign('cart_id')->references('cart_id')->on('cart_databases');
            $table->unsignedInteger('product_variant_id');
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10,2);
            $table->dateTime('reserved_at')->nullable();

            // Keep the composite unique constraint to prevent duplicates
            $table->unique(['cart_id', 'product_variant_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_detail_databases');
    }
};
