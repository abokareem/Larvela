<?php
/**
 * \class	OrderPlaced
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-22
 *
 *
 * [CC]
 * 
 * \addtogroup Transactional
 * OrderPlaced - Provides a hook when an Order is placed.
 * - Send a txt email to the store owner.
 * - Payment may not yet have been made.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Customer;




/**
 * \brief an Order has been placed, o we have a point here where we can insert additional business logic.
 * - Order will still need to be picked and dispatched.
 * - Customer email sent via a Mailable job.
 */
class OrderPlaced implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;

/**
 * The store object from the database tables stores, has all the details about the selected store.
 * @var mixed $store
 */
protected $store;


/**
 * The order object fromt eh database table "orders"
 * @var mixed $order
 */
protected $order;


/**
 * The email address to send to.
 * @var string $email
 */
protected $email;



    /**
     * Create a new job instance and save store and email details away.
	 *
     * @param  mixed	$store	The Store object.
     * @param  string	$email 	email address of customer
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
	 * provide a point for additional business logic to be inserted.
	 * - Send an email to the store admin.
	 *
     * @return void
     */
    public function handle()
    {
		$admin_user = Customer::find(1);
		$from = $this->store->store_sales_email;
		$subject = "[LARVELA] Order #".$this->order->id." placed -> message sent to [".$this->to."]";
		$text = Order #".$this->order->id." placed and email sent to [".$tis->email."]";
		dispatch(new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
    }
}
