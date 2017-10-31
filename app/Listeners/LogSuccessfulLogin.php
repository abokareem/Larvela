<?php
/**
 * \class	LogSuccessfulLogin
 * @date	2017-07-19
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 * [CC]
 */
namespace App\Listeners;


use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;



use App\Traits\Logger;


/**
 * \brief Event Listener for sucessful logins
 *
 * todo - update user table to record login attempt
 * send email if flagged, flag needs to be added somewhere.
 */
class LogSuccessfulLogin
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
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
		$text = "Login attempt OK -> ".print_r($event,true);
		$this->LogMsg($text);
    }
}
