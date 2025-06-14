<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('discounts:clear-product-expired')->dailyAt('00:00');
        $schedule->command('clean:temp-uploads')->daily();
        $schedule->command('orders:auto-approve')->everyMinute();
        $schedule->command('inventory:check-low-stock')->daily();
        $schedule->command('voucher:send-to-top-customers')->monthly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
