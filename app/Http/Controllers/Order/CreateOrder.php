<?php
/**
 * @class	CreateOrder
 * @author	Sid Young <sid@off-grif-engineering.com>
 * @date	2017-01-10
 * @version	1.0.4
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
*/
namespace App\Http\Controllers\Order;


use Auth;
use Input;
use Session;
use Request;
use Redirect;
use App\Http\Requests;
use Illuminate\Routing\Route;
use App\Http\Controllers\Controller;



use App\User;
use App\Models\Cart;
use App\Models\Order;

use App\Traits\Logger;



/**
 * @brief CreateOrder - Controller to handle a user creating an order.
 */
class CreateOrder extends Controller
{
use Logger;


	/**
	 * Open log file
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("CreateOrder");
		$this->LogStart();
	}


	/**
	 * Close log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}



	/**
	 * Given the cart and customer data, create an order.
	 *
	 *
	 *
	 * @param	App\Models\Customer	$customer
	 * @param	App\Models\CartData	$cartdata
	 * @param	App\Models\CartItem	$cartitems
	 * @param	string	$payment_ref
	 * @return	mixed
	 */
	public function CreateOrder($customer,$cartdata,$cartitems,$payment_ref)
	{
		$this->LogFunction("CreateOrder()");
		$this->LogMsg("Customer [".$customer->customer_email."]");
		
		$order = new Order;
		$order->order_ref = $payment_ref;
		$order->order_src = "AJAX";
		$order->order_cart_id = $cartdata->cd_cart_id;
		$order->order_cid = $customer->id;
		$order->order_status = "W";     # waiting to complete order (paid and dispatched)
		$order->order_shipping_method = $cartdata->cd_shipping_method;
		$order->order_shipping_value = $cartdata->cd_shipping;
		$order->order_value = $cartdata->cd_total;
		$order->order_payment_status = sizeof($payment_ref)>0 ? "P" : "D";
		$order->order_dispatch_status = "W";
		$order->order_date = date("Y-m-d");
		$order->order_time = date("H:i:s");
		$order->save();
		$this->LogMsg("inserted order id [".$order->id."]");
		return $order;
	}
}

