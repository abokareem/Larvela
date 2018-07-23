<?php
/**
 * \class	AbandonedCartEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-07-20
 *
 * [CC]
 *
 * \addtogroup CartAbandonment
 * AbandonedCartEmail - Send a templated email after the Customer has abandond the cart for a period of time.
 * - Usually sent 24 hours later.
 * - Does not do anything to the cart.
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
use App\Models\Users;
use App\Models\Cart;
use App\Models\Store;
use App\Models\CartItem;
use App\Models\Product;


use App\Traits\Logger;


/**
 * @brief Send a templated email after the Customer has abandond the cart for a period of time.
 */
class AbandonedCart extends Mailable
{
use Queueable, SerializesModels;
use Logger;

/**
 *
 * @var string $hash
 */
public $hash;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
public $store;


/**
 * The email address to send to
 * @var string $email
 */
public $email;


/**
 * The Cart object
 * @var mixed $cart
 */
public $cart;


/**
 *
 * @var	mixed $customer
 */
public $customer;



/**
 * @var string template
 */
protected $template;


private $ACTION="cart_abandoned";



    /**
     *
	 *
     * @param  $email     string - email address of customer
     * @param  $cart
     * @param  $store     mixed - store data collection
     * @return void
     */
    public function __construct($store, $email, $cart)
    {
		$this->store = $store;
		$this->email = $email;
		$this->cart = $cart;
		
		$this->customer = Customer::where('customer_email', $email)->first();
		$this->template = "Mail.".$this->store->store_env_code.".".$this->ACTION;
		$this->hash = "2874-".hash('ripemd160', $email.$store->store_env_code);
    }



    /**
     * Send email using the template
	 *
	 *
     * @return void
     */
    public function build()
    {
		$subject = "You Abandonded your cart!";
		return $this->from($this->store->store_sales_email,$this->store->store_name)->subject($subject)->view($this->template);
    }
}