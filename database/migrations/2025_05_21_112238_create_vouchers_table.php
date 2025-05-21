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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vouchers_code', 10)->unique();
            $table->text('vouchers_description');
            $table->decimal('vouchers_percent_discount', 10,3);
            $table->decimal('vouchers_max_discount', 10,3);
            $table->decimal('vouchers_min_order_amount', 10,3);
            $table->datetime('vouchers_start_date');
            $table->datetime('vouchers_end_date');
            $table->unsignedInteger('vouchers_usage_limit')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
