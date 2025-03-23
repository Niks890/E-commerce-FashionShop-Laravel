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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('address')->nullable();
            $table->decimal('shipping_fee', 10,2)->default(0);
            $table->decimal('total', 10,2)->nullable();
            $table->string('note', 100)->nullable();
            $table->string('receiver_name', 200)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('status', 50)->nullable();
            $table->float('VAT', 10,2)->default(0.1);
            $table->string('payment', 200)->nullable(); //Phuong thuc thanh toan
            $table->string('reason', 200)->nullable();
            $table->string('transaction_id', 50)->nullable();
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
