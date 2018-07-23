<?php
/**
 * \class	OrderPaid
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-10-04
 *
 * [CC]
 *
 * \addtogroup Transactional
 * OrderPaid - Provide an entry point for additional business logic.
 * - Notify the customer that their order has been updated to PAID
 */
namespace App\Jobs;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\Jobs\EmailUserJob;



/**
 * \brief Send a templated email confirming dispatching of the order.
 * Fetch and send the templated email
 *
 * {INFO_2017-10-28} OrderPaid.php - Moved template to ./store_env_code/...
 */
class OrderPaid extends Job implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var mixed $store
 */
protected $store;


/**
 * The order object.
 * @var mixed $order
 */
protected $order;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $email
 */
protected $email;





    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using ORDER_DISPATCHED action 
	 *
     * @param	mixed	$store	The current Store
     * @param	string	$email	email address of customer
     * @param	mixed	$order	The relevant Order
     * @return	void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->order = $order;
		$this->email = $email;
	}



    /**
	 * Provide a hook for additional business logic.
	 * - Email the store admin when an order is paid.
	 *
     * @return void
     */
    public function handle()
    {
		$text = "Notice: Order ".$this->order->id." has been Paid. Email dispatched to: ".$this->email;
		$subject = "[LARVELA] Order Paid email sent to [".$this->email."]";
		$from = $this->store->store_sales_email;

		$admin_user = Customer::find(1);
		dispatch( new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
    }
}
