<?php
/**
 * \class	AutoSubscribeJob
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-07-23
 * \version	1.0.1
 *
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 * \addtogroup Subscription
 * AutoSubscribeJob - Subscribe the new Customer to the Subscriptions table and
 * provide a place where additional business logic can be called.
 */
namespace App\Jobs;

use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;



use App\Models\SubscriptionRequest;
use App\Traits\Logger;
use App\Traits\NotifyAdminTrait;


/**
 * \brief Subscribe the new Customer to the Subscriptions table and
 * provide a place where additional business logic can be called.
 *
 ( {INFO_2019-07-25} AutoSubscribeJob - Added more logging and notify admin call.
 */
class AutoSubscribeJob implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;
use NotifyAdminTrait;
use Logger;


/**
 * The Customer email address
 * @var string $email
 */
protected $email;


/**
 * The Store relevant to the subscription request
 * @var mixed $store
 */
protected $store;



    /**
	 *============================================================
     * Store away the passed in parameters 
	 *============================================================
	 *
     * @param 	mixed	$store	- The Store object.
     * @param	string	$email	- The customer email address.
     * @return	void
     */
    public function __construct($store, $email)
    {
		$this->setFileName("larvela-jobs");
		$this->setClassName("AutoSubscribeJob");
		$this->LogStart();
		$this->store = $store;
		$this->email = email;
    }


	/**
	 *============================================================
	 * Cleanup Log
	 *============================================================
	 *
	 *
     * @return	void
	 */
	public function __destruct()
	{
		$this->LogStart();
	}



    /**
	 *============================================================
	 * Subbscribe the email automatically and provide a place for 
	 * additional Business Logic to be called.
	 *============================================================
	 *
	 *
     * @return integer
     */
    public function handle()
    {
		$this->SubscribeEmail($this->email);
		$text = "Customer [".$this->email."] has been auto-subscribed";
		$subject = "[LARVELA] Auto subscribe for ".$this->email;
		$this->NotifyAdmin($this->store, $subject, $text);

		#
		# Your code here - suggestion - use Queued Jobs for async operations.
		#

		return 0;
    }



	/**
	 *============================================================
	 * Subscribe the user to the subscription request table and 
	 * return the row ID.
	 *============================================================
	 *
	 * @param	string	$email
	 * @return	integer
	 */
	protected function SubscribeEmail( $email )
	{
		$this->LogFunction("SubscribeEmail()");
		$this->LogMsg("Subscribe: [".$email."]");

		$today = date("Y-m-d");
		$o = new SubscriptionRequest;
		$o->sr_email = $email;
		$o->sr_status = "C";
		$o->sr_process_value = 0;
		$o->sr_date_created = date("Y-m-d");
		$o->sr_date_updated= date("Y-m-d");
		$o->save();
		$this->LogMsg("Saved ID [".$o->id."]");
		return $o->id; 
	}
}
