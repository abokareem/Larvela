<?php
/**
 * \class	OrderCancelled
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-09-20
 *
 *
 * [CC]
 * 
 * \addtogroup Orders
 * OrderCancelled - An order has been cancelled so this Job provides a point to do additional work.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Store;
use App\Models\Customer;
use App\Models\Order;



/**
 * \brief Provide a point to do additional work when an  order has been cancelled.
 */
class OrderCancelled implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;


/**
 * The Store object for the currently selected Store.
 * @var mixed $store
 */
protected $store;


/**
 * The email of the customer who the order belongs to.
 * @var string $email
 */
public $email;


/**
 * The Order object from the database table "orders"
 * @var mixed $order
 */
protected $order;


    /**
     * Create a new job instance
	 * - Save Store, email and Order details.
	 *
     * @param  mixed	$store	The current Store.
     * @param  string	$email 	email address of customer.
     * @param  mixed	$order	The Order object.
     * @return void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->email = $email;
		$this->order = $order;
    }



    /**
	 * Place holder for additonal business logic
	 * -  Email the store admin that an Order has been cancelled.     
     * @return void
     */
    public function handle()
    {
		$text = "Notice: Order ".$this->order->id." has bee cancelled. Email sent to: ".$this->email;
		$subject = "[LARVELA] Order Cancelled email sent to [".$this->email."]";
		$from = $this->store->store_sales_email;

		$admin_user = Customer::find(1);
		dispatch( new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
    }
}
