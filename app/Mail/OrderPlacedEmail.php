<?php
/**
 * \class	OrderPlacedEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-04-08
 *
 *
 * [CC]
 * 
 * \addtogroup Transactional
 * OrderPlacedEmail - The customer has placed an order, send an email, payment may not yet have been made. Uses the mailable interface.
 */
namespace App\Mail;

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
 * \brief Send a templated email confirming placement of the order in the system.
 * - Order will still need to be picked and dispatched.
 * - Other jobs will send additional status emails until order is "completed".
 */
class OrderPlacedEmail extends Mailable
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
 * The OrderItem rows from the DB for this Order.
 * @var mixed $order_items
 */
public $order_items;


/**
 * The email address to send to.
 * @var string $email
 */
public $email;


/**
 * The Customer object
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
private $ACTION="order_placed";


public $hash;

    /**
     * Store data required to send email.
	 *
     * @param  mixed	$store	Store data (object)
     * @param  string	$email 	email address of customer
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
		$this->hash = "2874-".hash('ripemd160', $email.$store->store_env_code);
    }



    /**
     * Fetch the data and pass into the view
	 *
     * @return void
     */
    public function build()
    {
		$subject = $this->store->store_name." - Order #".$this->order->id." placed!";
        return $this->from($this->store->store_sales_email,$this->store->store_name)->subject($subject)->view($this->template); 
    }
}
