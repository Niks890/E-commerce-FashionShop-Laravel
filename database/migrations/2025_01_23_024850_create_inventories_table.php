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
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('total', 10,2)->nullable();
            $table->decimal('vat', 10,2)->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['approved', 'pending'])->default('pending');
            $table->unsignedInteger('provider_id');
            $table->unsignedInteger('staff_id');
            $table->foreign('provider_id')->references('id')->on('providers');
            $table->foreign('staff_id')->references('id')->on('staff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
