<?php
/**
 * \class	OrderPaidEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-04-08
 *
 *
 * [CC]
 * 
 * \addtogroup Transactional
 * OrderPaidEmail - The payment for the order has been acceptaed and is now marked as paid in the orders table. Send the customer an email using the mailable interface.
 */
namespace App\Mail;


use Hash;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;



/**
 * \brief Send a templated email confirming payment of the order.
 */
class OrderPaidEmail extends Mailable
{
use Queueable, SerializesModels;



/**
 * The store object from the database tables stores, has all the details about the selected store.
 * @var mixed $store
 */
public $store;


/**
 * The order object from the database table "orders"
 * @var mixed $order
 */
public $order;


/**
 * The order_items from order
 * @var mixed $order_items
 */
public $order_items;


/**
 * The email address to send the email to.
 * @var string $email
 */
public $email;


/**
 * The customer details.
 * @var mixed $customer
 */
public $customer;


/**
 * The view email template to use.
 * @var string template
 */
protected $template;

/**
 * The template to use
 * @var string $ACTION
 */
private $ACTION="order_paid";


/**
 * The hash for unscribing in footer if set
 * @var string $hash
 */
public $hash;

    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using CONFIRM_SUBSCRIPTION action 
	 *
     * @param  mixed	$store	Store data (object)
     * @param  string	$email 	email address of customer
     * @param  mixed	$order	the order details
     * @return void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->email = $email;
		$this->order = $order;
		$this->customer = Customer::where('customer_email', $email)->first();
		$this->order_items = OrderItem::where('order_item_oid',$this->order->id)->get();
		$this->template = "Mail.".$this->store->store_env_code.".".$this->ACTION;
		$this->hash = $this->customer->id."-".hash('ripemd160', $email.$store->store_env_code);
    }



    /**
     * Fetch the data and pass into the view
	 *
     * @return void
     */
    public function build()
    {
		$subject = $this->store->store_name." - Order #".$this->order->id." Paid!";
        return $this->from($this->store->store_sales_email,$this->store->store_name)->subject($subject)->view($this->template); 
    }
}
