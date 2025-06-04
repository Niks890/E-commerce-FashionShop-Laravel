<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanTempUploads extends Command
{
    protected $signature = 'clean:temp-uploads';
    protected $description = 'Clean up temporary upload files older than 24 hours';

    public function handle()
    {
        $files = Storage::files('temp_uploads');

        foreach ($files as $file) {
            if (now()->diffInHours(Storage::lastModified($file)) > 24) {
                Storage::delete($file);
            }
        }

        $this->info('Cleaned up '.count($files).' temporary files');
    }
}
