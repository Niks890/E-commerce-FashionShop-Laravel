<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\CloudinaryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $productId,
        public string $filePath // Đường dẫn đầy đủ đến file
    ) {}

    public function handle(CloudinaryService $cloudinaryService)
    {
        $product = Product::findOrFail($this->productId);

        try {
            // Kiểm tra file tồn tại
            if (!file_exists($this->filePath)) {
                throw new \Exception("File not found at path: {$this->filePath}");
            }

            // Upload lên Cloudinary
            $uploadResult = $cloudinaryService->uploadImage(
                $this->filePath,
                'product_images'
            );

            // Cập nhật URL ảnh
            $product->update(['image' => $uploadResult['url']]);

            // Xóa file tạm sau khi xử lý
            Storage::delete(str_replace(storage_path('app'), '', $this->filePath));

        } catch (\Exception $e) {
            Log::error("Product image processing failed - Product ID: {$this->productId}, Error: " . $e->getMessage());
            throw $e; // Để queue thử lại
        }
    }
}
