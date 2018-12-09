<?php
/**
 * \class	AbandonedWeekOldCartEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-07-20
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

use App\User;
use App\Models\Cart;
use App\Models\Store;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Customer;


use App\Traits\Logger;


/**
 * @brief Send a templated email after the Customer has abandond the cart for a period of time.
 */
class AbandonedWeekOldCartEmail extends Mailable
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


private $ACTION="cart_7day_abandoned";



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
		$this->hash = $this->customer->id."-".hash('ripemd160', $email.$store->store_env_code);
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
