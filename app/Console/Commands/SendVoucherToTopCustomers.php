<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\VoucherUsage;
use Illuminate\Support\Facades\Mail;
use App\Mail\VoucherMail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendVoucherToTopCustomers extends Command
{
    protected $signature = 'voucher:send-to-top-customers
                            {--top=5 : Number of top customers to select}
                            {--days=30 : Look back period in days}';
    protected $description = 'Automatically send voucher to top purchasing customers';

    public function handle()
    {
        $top = $this->option('top');
        $days = $this->option('days');

        // 1. Tìm voucher phù hợp tự động
        $voucher = $this->findSuitableVoucher();

        if (!$voucher) {
            $this->error('Không tìm thấy voucher phù hợp để tặng');
            Log::warning('Không tìm thấy voucher phù hợp để tặng cho top khách hàng');
            return;
        }

        $this->info("Sử dụng voucher: {$voucher->vouchers_code} (ID: {$voucher->id})");

        // 2. Lấy top khách hàng có tổng giá trị đơn hàng cao nhất trong khoảng thời gian
        $topCustomers = Customer::select('customers.*')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.status', 'Giao hàng thành công')
            ->where('orders.created_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw('customers.*, SUM(orders.total) as total_spent')
            ->groupBy('customers.id')
            ->orderByDesc('total_spent')
            ->take($top)
            ->get();

        if ($topCustomers->isEmpty()) {
            $this->info('Không tìm thấy khách hàng nào trong khoảng thời gian này');
            return;
        }

        $this->info("Chuẩn bị gửi voucher tới {$topCustomers->count()} khách hàng thân thiết...");

        $successCount = 0;
        foreach ($topCustomers as $customer) {
            try {
                // Kiểm tra xem khách hàng đã có voucher này chưa
                $existingVoucher = VoucherUsage::where('customer_id', $customer->id)
                    ->where('voucher_id', $voucher->id)
                    ->first();

                if ($existingVoucher) {
                    $this->info("Khách hàng ID {$customer->id} đã nhận voucher này trước đây. Bỏ qua...");
                    continue;
                }

                // Tạo bản ghi sử dụng voucher
                VoucherUsage::create([
                    'customer_id' => $customer->id,
                    'voucher_id' => $voucher->id,
                    'order_id' => null,
                    'used_at' => null,
                ]);

                // Giảm số lượng voucher nếu có giới hạn
                // if ($voucher->vouchers_usage_limit > 0) {
                //     $voucher->decrement('vouchers_usage_limit');
                // }

                // Gửi email
                $message = "Chúc mừng bạn là một trong những khách hàng thân thiết nhất của chúng tôi! Đây là voucher đặc biệt dành riêng cho bạn.";
                Mail::to($customer->email)->send(new VoucherMail($customer, $voucher, $message, $voucher->vouchers_end_date));

                $this->info("Đã gửi voucher thành công tới khách hàng ID: {$customer->id} ({$customer->email})");
                $successCount++;

                Log::info("Đã gửi voucher cho khách hàng thân thiết", [
                    'customer_id' => $customer->id,
                    'voucher_id' => $voucher->id,
                    'total_spent' => $customer->total_spent,
                ]);
            } catch (\Exception $e) {
                Log::error('Lỗi khi gửi voucher cho khách hàng: ' . $e->getMessage(), [
                    'customer_id' => $customer->id,
                    'voucher_id' => $voucher->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Lỗi khi gửi voucher cho khách hàng ID: {$customer->id}. Lỗi: " . $e->getMessage());
            }
        }

        $this->info("Hoàn thành! Đã gửi voucher tới {$successCount} khách hàng.");
    }

    /**
     * Tìm voucher phù hợp để tặng tự động
     */
    protected function findSuitableVoucher()
    {
        return Voucher::where('vouchers_end_date', '>', Carbon::now())
            ->where(function($query) {
                $query->where('vouchers_usage_limit', '>', 0)
                      ->orWhereNull('vouchers_usage_limit');
            })
            ->orderBy('vouchers_end_date', 'asc') // Ưu tiên voucher sắp hết hạn
            ->first();
    }
}
