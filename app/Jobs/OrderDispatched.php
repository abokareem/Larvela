<?php
/**
 * \class	OrderDispatched
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-22
 *
 * [CC]
 *
 * \addtogroup Transactional
 * OrderDispatched - Provide an entry point for additonal business logic when an Order is dispatched.
 * - Currently email the store admin that an order has been disatched.
 */
namespace App\Jobs;

use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Order;
use App\Models\Store;
use App\Models\Customer;

use App\Jobs\EmailUserJob;


/**
 * \brief Send an email to the admin that an Order has been dispatched.
 */
class OrderDispatched implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;

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
     * Provide an entry point for additional business logic.
	 * - Notify the store admin that a dispatch email has gone out.
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
