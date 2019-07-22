<?php
/**
 * \class	AbandonedWeekOldCart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-08-30
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
 * \addtogroup	Cart_Abandonment
 * AbandonedWeekOldCart - Place holder for additonal business Logic.
 * - CRON Calls this via ProcessAbandonedCart Job.
 * - Cart has been abandoned for 1 week now.
 * - Email sent via a Mailable Job.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\Jobs\EmailUserJob;

use App\Models\Cart;
use App\Models\Store;
use App\Models\Customer;

use App\Traits\Logger;

/**
 * \brief Cart has been abandoned for 1 week, execute any additional business logic here.
 */
class AbandonedWeekOldCart implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;
use Logger;



/**
 * The Store object 
 * @var mixed $store
 */
protected $store;


/**
 * The email address of the customer
 * @var string $email
 */
protected $email;


/**
 * The Cart object
 * @var mixed $cart
 */
protected $cart;




    /**
	 *============================================================
     * Save passed in parameters for later use. 
	 *============================================================
	 *
     * @param  $store	mixed - The Store object.
     * @param  $email	string - email address of customer.
     * @param  $cart	mixed - The Cart object.
     * @return void
     */
    public function __construct($store, $email, $cart)
    {
		$this->setFileName("larvela-jobs");
		$this->setClassName("AbandonedWeekOldCart");
		$this->LogStart();
		$this->store = $store;
		$this->email = $email;
		$this->cart  = $cart;
    }



	/**
	 *============================================================
	 * Close off the log
	 *============================================================
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}



    /**
	 *============================================================
	 * Send an email to the Store administrator and provide a
	 * place for additional business logic.
	 *============================================================
	 *
     * @return integer
     */
    public function handle()
    {
		$this->LogFunction("handle()");
		#
		# Your business logic here - suggestion - use Queued Jobs for asynchronous tasks.
		#


		#
		# Email the store administrator
		#
		$from = $this->store->store_sales_email;	
		$subject = "[LARVELA] Week Old Abandoned cart message sent to [".$this->email."]";
		$text = "Notice: Cart ".$this->cart->id." abandoned 1 week ago by customer ".$this->email;

		$this->LogMsg("=========================");
		$this->LogMsg("                         ");
		$this->LogMsg("   Change to Mailable    ");
		$this->LogMsg("                         ");
		$this->LogMsg("=========================");

		$admin_user = Customer::find(1);
		dispatch(new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
		$this->LogMsg("Done!");
		return 0;
    }
}
