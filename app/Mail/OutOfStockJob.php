<?php
/**
 * \class	OutOfStockJob
 * \date	2016-12-06
 * \author	Sid Young <sid@off-grid-engineering.com>
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
 * \addtogroup Product_Replenishment
 * OutOfStockJob - Email notification for store admins that the product is now at qty 0.
 */
namespace App\Mail;

use Hash;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use App\Models\Store;
use App\Models\Customer;
use App\Models\Product;




/**
 * \brief Send an Out of stock notification to the store owner when the last of a product is purchased.
 */
class OutOfStockJob extends Mailable
{
use Queueable, SerializesModels;



/**
 * @var mixed $customer
 */
public $customer;

/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var mixed $store
 */
public $store;


/**
 * The email address to send the email to.
 * @var string $to
 */
public $email;


/**
 * The subject line for this email
 * @var string $hash
 */
public $hash;


/**
 * The view email template to use.
 * @var string template
 */
protected $template;


/**
 * The Product details
 * @var  mixed $product
 */
protected $product;


/**
 * The template to get
 * @var string $ACTION
 */
private $ACTION="PRODUCT_OUT_OF_STOCK";

    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using PRODUCT_OUT_OF_STOCK action 
	 *
     * @param	mixed	$store		store data collection
     * @param	string	$email		email address of customer
     * @param	mixed	$product
     * @return	void
     */
    public function __construct($store, $email, $product)
    {
		$this->store = $store;
		$this->product = $product;
		$this->email = $email;
		$this->customer = Customer::where('customer_email',$email)->first();
		$this->template = "Mail".$store->store_env_code.".".$this->ACTION;
		$this->hash = $this->customer->id."-".hash('ripemd160',$email.$store->store_env_code);
    }



    /**
     * Fetch the data and pass into the view
	 *
     * @return void
     */
    public function build()
    {
		$subject = $this->store->store_name." - Product Out Of Stock - ".$this->product->prod_title;
		return $this->from($this->store->storesales_email,$this->store->store_name)->subject($subject)->view($this->template);
	}
}
