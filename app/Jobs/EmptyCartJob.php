<?php
/**
 * \class	EmptyCartJob
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-01
 * \version	1.0.1
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
 * \addtogroup Internal
 * EmptyCartJob - Deletes all cart items and cart data.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Traits\Logger;

use	App\Models\Cart;
use	App\Models\CartItem;
use	App\Models\CartData;


/**
 * \brief Deletes all cart items and the cart data row. Updates the cart last access date accordingly.
 */
class EmptyCartJob extends Job 
{
use Logger;


/**
 * The row ID of the cart.
 * @var int $cart_id
 */
protected $cart_id;



    /**
     * Create a new job instance.
     *
     * @param	integer	$cart_id	The row ID from the carts table
     * @return	void
     */
    public function __construct($cart_id)
    {
		$this->setFileName("larvela");
		$this->setClassName("EmptyCartJob");
		$this->LogStart();

		$this->cart_id = $cart_id;
    }



    /**
     * Log the job has cleaned up
     *
     * @return	void
     */
	public function __destruct()
	{
		$this->LogEnd();
	}




    /**
     * Execute the job, remove all items and data, then update the cart "updated_at" timestamp.
     *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("handle()");

		$cart_id = $this->cart_id;

		$cart = Cart::find($cart_id);
		
		$items = CartItem::where('cart_id',$cart_id)->get();
		$this->LogMsg("Remove cart items.");
		foreach($items as $item)
		{
			$this->LogMsg("\-- Removing cart item ID [".$item->id."]");
			$item->delete();
		}

		$data = CartData::where('cd_cart_id',$cart_id)->get();
		foreach($data as $d)
		{
			$d->delete();
		}
		$this->LogMsg("Cart Data and Items now removed");
		$cart->touch();
	}
}
