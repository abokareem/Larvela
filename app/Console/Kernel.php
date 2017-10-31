<?php
/**
 * \class	Kernel
 * 
 * Using the Consol Kernel Scheduling component to remove the need for external 
 * scheduling and cron files.
 */
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;




/**
 * \brief Using the Consol Kernel Scheduling component to remove the need for external 
 * scheduling and cron files.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];





    /**
     * Define the application's command schedule. We will call multiple Jobs
     *
     * {INFO_2017-10-24} Added scheduled job BuildReleaseInfo to run at 23:30 daily
	 *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		$schedule->call('\App\Jobs\ProcessAbandonedCart@handle')->dailyAt('10:30');
		$schedule->call('\App\Jobs\UpdateCartLocks@handle')->everyMinute();
		$schedule->call('\App\Jobs\CheckPendingOrders@handle')->dailyAt('5:00');
		$schedule->call('\App\Jobs\BuildReleaseInfo@handle')->dailyAt('23:30');
    }
}
