<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Carbon\Carbon;

class AutoApproveOrders extends Command
{
    protected $signature = 'orders:auto-approve';
    protected $description = 'Tự động duyệt các đơn hàng đã được tạo hơn 30 phút';

    public function handle()
    {
        $threshold = Carbon::now()->subMinutes(30);

        $orders = Order::where('status', 'Chờ xử lý') // Giả sử trạng thái ban đầu là "Chờ duyệt"
            ->where('created_at', '<=', $threshold)
            ->get();

        foreach ($orders as $order) {
            $order->status = "Đã xử lý";
            $order->save();

            $orderhistories = new OrderStatusHistory();
            $orderhistories->order_id = $order->id;
            $orderhistories->note = "Tự động duyệt";
            $orderhistories->status = "Đã xử lý";
            $orderhistories->save();

            $this->info("Đã tự động duyệt đơn hàng ID: {$order->id}");
        }

        $this->info("Đã hoàn thành tự động duyệt đơn hàng. Tổng cộng: " . count($orders));
    }
}
