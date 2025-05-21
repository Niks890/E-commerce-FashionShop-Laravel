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
        Schema::create('image_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->unsignedInteger('product_variant_id');
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_variants');
    }
};
