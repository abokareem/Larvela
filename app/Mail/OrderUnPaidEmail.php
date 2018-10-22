<?php
/**
 * \class	OrderUnPaidEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-04-08
 *
 *
 * [CC]
 * 
 * \addtogroup Transactional
 * OrderUnPaidEmail - The customer has placed an order, send an email, payment may not yet have been made. Uses the mailable interface.
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
 * \brief Send a templated email confirming that payment has not yet been received.
 * - Usually dispatched via a CRON Job after a time period.
 */
class OrderUnPaidEmail extends Mailable
{
use Queueable, SerializesModels;



/**
 * The store object from the database tables stores, has all the details about the selected store.
 * @var mixed $store
 */
public $store;


/**
 * The Order object from the database table "orders"
 * @var mixed $order
 */
public $order;


/**
 * The OrderItem from Order
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
private $ACTION="order_unpaid";


/**
 * The hash for unscribing in footer if set
 * @var string $hash
 */
public $hash;

    /**
     * Save the data and retrieve additional data as needed for Mail class
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
		$subject = $this->store->store_name." - Order #".$this->order->id." is still un-paid!";
        return $this->from($this->store->store_sales_email,$this->store->store_name)->subject($subject)->view($this->template); 
    }
}
