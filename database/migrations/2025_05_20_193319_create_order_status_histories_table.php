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
                'Đã duyệt',
                'Đang gửi cho đơn vị vận chuyển',
                'Đang giao hàng',
                'Giao hàng thành công',
                'Đã bị huỷ'
            ]);
            $table->text('note')->nullable();
            $table->foreignId('updated_by')->references('id')->on('staff')->nullable();
            $table->foreignId('order_id')->references('id')->on('orders');
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
