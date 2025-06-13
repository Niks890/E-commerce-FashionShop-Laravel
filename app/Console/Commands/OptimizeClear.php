<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class OptimizeClear extends Command
{
    protected $signature = 'optimize:clear';
    protected $description = 'Dọn dẹp cache, session, log và file tạm';

    public function handle()
    {
        // Xóa cache Laravel
        Artisan::call('cache:clear');
        $this->info('Đã xóa cache.');

        // Xóa cache view
        Artisan::call('view:clear');
        $this->info('Đã xóa cache view.');

        // Xóa session cũ (nếu dùng file-based session)
        // $sessionFiles = Storage::disk('sessions')->files();
        // foreach ($sessionFiles as $file) {
        //     if (Storage::disk('sessions')->lastModified($file) < now()->subWeek()->getTimestamp()) {
        //         Storage::disk('sessions')->delete($file);
        //     }
        // }
        // $this->info('Đã dọn dẹp session cũ.');

        // Xóa file tạm trong thư mục storage/temp
        $tempFiles = Storage::disk('local')->files('temp');
        Storage::disk('local')->delete($tempFiles);
        $this->info('Đã xóa file tạm.');

        // Xóa log cũ (giữ lại 7 ngày gần nhất)
        $logFiles = glob(storage_path('logs/*.log'));
        foreach ($logFiles as $log) {
            if (filemtime($log) < now()->subDays(7)->getTimestamp()) {
                unlink($log);
            }
        }
        $this->info('Đã dọn dẹp log cũ.');

        $this->info('✅ Dọn dẹp hệ thống hoàn tất!');
    }
}
