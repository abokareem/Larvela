<?php
/** 
 * \class	LogRegisteredUser
 * @date	2017-07-19
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 *
 *
 * [CC]
 */
namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Traits\Logger;



/**
 * \brief Event Listener called when a user registers
 */
class LogRegisteredUser
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
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
		$text = "New user registration --> ".print_r($event, true);
		$this->LogMsg($text); 

		#
		# @todo Dispatch welcome here
		#
    }
}
