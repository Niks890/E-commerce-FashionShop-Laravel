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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', [
                'Chờ xử lý',
                'Đã xử lý',
                'Đã duyệt',
                'Đã thanh toán',
                'Đã gửi cho đơn vị vận chuyển',
                'Đang giao hàng',
                'Giao hàng thành công',
                'Đã huỷ đơn hàng'
            ]);
            $table->text('note')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('order_id')->nullable();
            $table->foreign('updated_by')->references('id')->on('staff');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
