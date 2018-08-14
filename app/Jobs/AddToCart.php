<?php
/**
 * \class	AddToCart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-08-09
 * \version	1.0.0
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
 * \addtogroup Cart
 * AddToCart - Entry point for additional business logic when an item is added to a cart.
 * - Send the store admin an email about item and cart.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;


/**
 * @brief Place holder for any business logic needed when an item has been added to a Cart.
 */
class AddToCart implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;

/**
 * The Store object
 * @var mixed $store
 */
protected $store;


/**
 * The Cart object
 * @var mixed $cart
 */
protected $cart;


    /**
     * Create a new job instance save store and email details away.
	 *
     * @param  $store	mixed - The Store object
     * @param  $cart	mixed - The Cart onject
     * @return void
     */
    public function __construct($store, $cart)
    {
		$this->store = $store;
		$this->cart  = $cart;
    }



    /**
	 * Pleace holder for aditional business logic to run prior to customer notification.
	 * - May run as Queue Job, so may execute before/durng or after Email gets sent.
     * @return void
     */
    public function handle()
    {
		$text = "Notice: Item added to Cart [] by [] ";
		$subject = "[LARVELA] Item added to Cart [".$this->cart->id."]";
		$from = $this->store->store_sales_email;

		$admin_user = Customer::find(1);
		dispatch( new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
    }

}
