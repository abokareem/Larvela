<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Traits\Logger;

/**
 * \brief Event handler for Login attempted PRE hook.
 */
class LogLoginAttempt
{
use Logger;




    /**
     * Create the event listener and setup logging support.
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
     * @param  Attempting  $event
     * @return void
     */
    public function handle(Attempting $event)
    {
		$text = "Event type ATTEMPTING --> ".print_r($event,true);
		$this->LogMsg($text); 
    }
}
