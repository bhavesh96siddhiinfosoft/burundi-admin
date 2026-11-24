<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected $commands = [
        //Commands\ResetFuelQuota::class,
    ];
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    protected function schedule(Schedule $schedule)
    {
        Log::info('Scheduler running');
        $schedule->command('activities:clean')->everyMinute();

       //$schedule->command('fuel:reset-quota')->everyFiveMinutes();
    }
    
}
