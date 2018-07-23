<?php
/**
 * \class	OrderDispatched
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-22
 *
 * [CC]
 *
 * \addtogroup Transactional
 * OrderDispatched - Notify the customer that their order has been dispatched.
 */
namespace App\Jobs;

use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Models\Order;
use App\Models\Store;
use App\Models\Customer;

use App\Jobs\EmailUserJob;
use App\Traits\TemplateTrait;


/**
 * \brief Send a templated email confirming dispatching of the order.
 *
 * Fetch and send the templated email, template mapping is: "ORDER_DISPATCHED"
 * {INFO_2017-10-28} OrderDispatched.php - Moved template to ./store_env_code/...
 */
class OrderDispatched extends Job 
{
use TemplateTrait;


/**
 * The order placed by the customer.
 * @var mixed $order
 */
protected $order;


/**
 * The store object from the database tables stores, has all the details about the selected store.
 * @var mixed $store
 */
protected $store;


/**
 * The email address 
 * @var string $email
 */
protected $email;





    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using ORDER_DISPATCHED action 
	 *
     * @param	mixed	$store	The Store object
     * @param	string	$email	email address of customer
     * @param	mixed	$order	The Order object
     * @return	void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->order = $order;
		$this->email = $email;
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using Job Dispatch.
	 *
     * @return void
     */
    public function handle()
    {
		$text = "Notice: Order ".$this->order->id." Dispatched to: ".$this->email;
		$subject = "[LARVELA] Order Dispatched email sent to [".$this->email."]";
		$from = $this->store->store_sales_email;

		$admin_user = Customer::find(1);
		dispatch( new EmailUserJob($admin_user->customer_email, $from, $subject, $text));

    }
}
