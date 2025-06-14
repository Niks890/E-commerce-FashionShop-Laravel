<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowStockAlert;
use App\Models\ProductVariant;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock';
    protected $description = 'Kiểm tra sản phẩm sắp hết hàng và gửi cảnh báo';

    public function handle()
    {
        // Lấy sản phẩm có ít nhất 1 biến thể tồn kho ≤ 5
        $lowStockProducts = Product::whereHas('ProductVariants', function ($query) {
            $query->where('stock', '<=', 5);
        })->with(['ProductVariants' => function ($query) {
            $query->where('stock', '<=', 5);
        }])->get();

        if ($lowStockProducts->isNotEmpty()) {
            foreach ($lowStockProducts as $product) {
                $this->info("Sản phẩm {$product->product_name} sắp hết hàng. Các biến thể:");
                foreach ($product->ProductVariants as $variant) {
                    $this->info("- {$variant->size}-{$variant->color}: Tổng số lượng tồn kho {$variant->stock}");
                }
            }
        } else {
            $this->info("Không có sản phẩm nào sắp hết hàng.");
        }
    }
}
