<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        Commands\Invoice::class,
        Commands\AutoReportCustomerInfo::class
    ];
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('invoice:add')->everyMinute();
       	//$schedule->command('invoice:add')->timezone('Asia/Phnom_Penh')->monthlyOn(20,'24:00');
       	//$schedule->command('invoice:add')->everyMinute();
        // $schedule->command('invoice:add')->everyMinute();
        $invoiceDayAuto = (object) config('cron-job.data');
        $day = $invoiceDayAuto?->invoiceDay ?? 1;
        $dayReportCustomerInfo = $invoiceDayAuto?->reportCustomerInfoDay ?? 1;
        $schedule->command('invoice:add')->timezone('Asia/Phnom_Penh')->monthlyOn($day,'00:00');
        $schedule->command('command:reportCustomerInfo')->timezone('Asia/Phnom_Penh')->monthlyOn($dayReportCustomerInfo,'00:00');
    }
    
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
