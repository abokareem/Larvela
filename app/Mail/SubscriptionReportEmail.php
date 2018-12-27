<?php
/**
 * \class	SubscriptionReportEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-12-24
 * \version	1.0.0
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
 * \addtogroup Reports
 * SubscriptionReportEmail - Sends a Reort email every day of the current state of the subscriptions.
 */
namespace App\Mail;


use Hash;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use App\Models\Store;
use App\Models\Customer;
use App\Models\SubscriptionStat;
use App\Models\SubscriptionRequest;


use App\Traits\Logger;


/**
 * \brief Send a templated email welcoming the Customer.
 *
 * Called when MyAccount used and a Customer record is inserted. This implies they have registered
 * on the site but not necessarily subscribed.
 */
class SubscriptionReportEmail extends Mailable
{
use Queueable, SerializesModels;
use Logger;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
public $store;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $email
 */
public $email;


/**
 * The has used in unsubscribing
 * @var string $hash
 */
public $hash;


/**
 * The filename/template to use.
 * @var string $template
 */
protected $template;


public $ACTION="subscription_report";

public $last_seven_days_stats;

public $this_months_stats;

public $last_months_stats;


    /**
	 *
     * @param  $store     mixed - store data collection
     * @param  $email     string - email address of customer
     * @return void
     */
    public function __construct($store, $email)
    {
		$this->store = $store;
		$this->email = $email;
		$this->customer = Customer::where('customer_email', $email)->first();
		$this->template = "Mail.".$this->store->store_env_code.".".$this->ACTION;
		$this->hash = $this->customer->id."-".hash('ripemd160', $email.$store->store_env_code);
    }



    /**
     * return the template for emailing
     * @return void
     */
    public function build()
    {
		$from = $this->store->store_sales_email;
		$subject = "7 Day subscription status report for ".$this->store->store_name;
		$to = date("Y-m-d");
		$from = date('Y-m-d', strtotime('-7 days'));
		$this->last_seven_days_stats = SubscriptionStat::whereBetween('subs_date_created',[$from,$to])->get();

		$from = date('Y-m-01');
		$this->this_months_stats = SubscriptionStat::whereBetween('subs_date_created',[$from,$to])->get();

		$from = date('Y-m-d', strtotime('first day of last month'));
		$to = date('Y-m-d', strtotime('last day of last month'));
		$this->last_months_stats = SubscriptionStat::whereBetween('subs_date_created',[$from,$to])->get();
		return $this->from($this->store->store_sales_email, $this->store->store_name)->subject($subject)->view($this->template);
	}


}
