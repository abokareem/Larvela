<?php
/**
 * \class	LogFailedLogin
 * \date	2017-09-01
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version	1.0.1
 *
 * 
 */
namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use App\Models\Store;
use App\Jobs\LoginFailed;


use App\Traits\Logger;


/**
 * \brief Triggered when framework detects a failed login.
 * - Need to kick off a job to send a failed email and
 * allow for the update of a security log somewhere?
 */
class LogFailedLogin
{
use Logger;



    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
		$this->setFileName("store-security");
    }



    /**
     * Handle the event.
     *
     *
     * {INFO_2017-09-01} Implemented Failed Login Capture and Reporting via LoginFailedEmail Job 
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
		$text = "Failed Login attempt! --> ".print_r($event,true);
		$this->LogMsg($text);

		$email = $event->credentials['email'];
		$password = $event->credentials['password'];

		$store = app('store');
		
		dispatch(new LoginFailed($store, $email));
    }
}
