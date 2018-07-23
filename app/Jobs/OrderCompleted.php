<?php
/**
 * \class	OrderCompleted
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-09-20
 *
 *
 * [CC]
 * 
 * \addtogroup  Transactional
 * OrderCompleted - This Job provides a place to execute additonal business logic when an Order is completed. 
 * - Currently emails the store admin that an order has been dispatched.
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

use App\Traits\Logger;




/**
 * \brief Execute additonal business logic when an Order is completed.
 */
class OrderCompleted implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;
use Logger;



/**
 * The store object from the database tables stores
 * @var mixed $store
 */
protected $store;


/**
 * The order object from the database table "orders"
 * @var mixed $order
 */
protected $order;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $email
 */
public $email;


    /**
     * Create a new Job instance
	 * - Save store and email details away.
	 *
     * @param  mixed	$store
     * @param  string	$email 	email address of customer
     * @param  mixed	$order
     * @return void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->email = $email;
		$this->order = $order;
    }



    /**
	 * Execute any extra business logic when an Order is Completed
	 *     
     * @return void
     */
    public function handle()
	{
		$text = "Notice: Order ".$this->order->id." Completed. Email sent to: ".$this->email;
		$subject = "[LARVELA] Order Completed email sent to [".$this->email."]";
		$from = $this->store->store_sales_email;

		$admin_user = Customer::find(1);
		dispatch( new EmailUserJob($admin_user->customer_email, $from, $subject, $text));

	}
}
