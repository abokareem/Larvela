<?php
/**
 * \class	OrderDispatchPending
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-22
 *
 * [CC]
 *
 * \addtogroup Transactional
 * OrderDispatchPending - Notify the customer that their order has been dispatched.
 */
namespace App\Jobs;

use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Models\Customer;
use App\Models\Store;

use App\Jobs\EmailUserJob;
use App\Traits\TemplateTrait;


/**
 * \brief Send a templated email confirming dispatching of the order.
 * Fetch and send the templated email, template mapping is: "ORDER_DISPATCHED"
 */
class OrderDispatchPending extends Job 
{
use TemplateTrait;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $email
 */
protected $email;

/**
 * The order object
 * @var mixed $order
 */
protected $order;


    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using ORDER_DISPATCHED action 
	 *
     * @param	mixed	$store	store data collection
     * @param	string	$email	email address of customer
     * @param	mixed	$order
     * @return	void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->email = $email;
		$this->order = $order;
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using Job Dispatch.
	 *
     * @return void
     */
    public function handle()
    {
		$subject = "[LARVELA] Order Dispatch Pending email sent to [".$this->to."]a;
		$text = "Notice Order Dispatch Pending email sent to [".$this->email."]";
		$from = $this->store->store_sales_email;
		$admin_user = Customer::find(1);
		dispatch(new EmailUserJob($admin_user->customer_email, $from, $subject, $text);
    }
}
