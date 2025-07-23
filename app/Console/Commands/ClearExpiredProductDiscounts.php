<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product; // Import model Product
use App\Models\Discount; // Import model Discount
use Carbon\Carbon; // Import Carbon

class ClearExpiredProductDiscounts extends Command
{
    /**
     * Tên và chữ ký của lệnh console.
     *
     * @var string
     */
    protected $signature = 'discounts:clear-product-expired'; // Tên lệnh bạn sẽ dùng để chạy

    /**
     * Mô tả lệnh console.
     *
     * @var string
     */
    protected $description = 'Sets discount_id to NULL for products linked to expired or inactive discounts.';

    /**
     * Thực thi lệnh console.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Bắt đầu gỡ bỏ khuyến mãi đã hết hạn cho sản phẩm...');

        // Lấy tất cả các ID khuyến mãi đã hết hạn hoặc không còn "active"
        // (Ngày kết thúc đã qua HOẶC trạng thái không phải 'active')
        $expiredOrInactiveDiscountIds = Discount::where('end_date', '<', Carbon::now())
                                                ->orWhere('status', '!=', 'active')
                                                ->pluck('id');

        // Nếu không có khuyến mãi nào cần xử lý, kết thúc sớm
        if ($expiredOrInactiveDiscountIds->isEmpty()) {
            $this->info('Không tìm thấy khuyến mãi hết hạn hoặc không hoạt động nào.');
            return Command::SUCCESS;
        }

        // Tìm tất cả sản phẩm đang liên kết với các khuyến mãi
        // và cập nhật discount_id của chúng thành NULL
        $updatedRows = Product::whereIn('discount_id', $expiredOrInactiveDiscountIds)
                                ->update(['discount_id' => null]);

        $this->info("Đã cập nhật thành công discount_id thành NULL cho {$updatedRows} sản phẩm.");
        return Command::SUCCESS;
    }
}
