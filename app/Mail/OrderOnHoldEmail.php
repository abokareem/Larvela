<?php
/**
 * \class	OrderOnHoldEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-04-08
 * \version	1.0.0
 *
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * 
 * \addtogroup Transactional
 * OrderOnHoldEmail - The order has been placed on hold due to stock shortages after payment or stock level issues.
 * Send an email to the customer using the mailable interface.
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
 * \brief Send a templated email indicating the order has been placed on hold.
 * - Can be on hold due to a payment issue
 * - Can be placed on hold if the stock levels do not match (manual).
 */
class OrderOnHoldEmail extends Mailable
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
private $ACTION="order_onhold";


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
		$subject = $this->store->store_name." - Order #".$this->order->id." now on hold!";
        return $this->from($this->store->store_sales_email,$this->store->store_name)->subject($subject)->view($this->template); 
    }
}
