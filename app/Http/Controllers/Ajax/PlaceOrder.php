<?php
/**
 * \class	PlaceOrder
 * \date	2018-12-06
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version 1.0.0
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
namespace App\Http\Controllers\Ajax;


use Auth;
use Input;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
#use Illuminate\Support\Facades\Request;
use App\Events\Larvela\PlaceOrderMessage;


use App\User;
use App\Models\Cart;
use App\Models\Order;
use App\Jobs\OrderPaid;
use App\Models\Customer;
use App\Models\CartData;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Jobs\OrderPlaced;
use App\Mail\OrderPaidEmail;
use App\Mail\OrderPlacedEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

use App\Traits\Logger;


/** 
 * \brief  Place Order is called to kick off the order process, with either a
 * paid or unpaid order initiated from Cart->Checkout->Confirm-(Payment/EFT)
 */
class PlaceOrder extends Controller
{
use Logger;


	/**
	 * Constuct a new cart and make sure we are authenticated before using it.
	 *
	 * @return	void
	 */ 
	public function __construct()
	{
		$this->setFileName("larvela-ajax");
		$this->setClassName("PlaceOrder");
		$this->LogStart();
	}


	/**
	 * Close of log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}



	
	/**
	 * Given the cart ID via an ajax call, Place an Order
	 *
	 * @param	integer	$id		Cart ID to update
	 * @return	array
	 */
	public function PlaceOrder(Request $request, $id)
	{
		$this->LogFunction("PlaceOrder($id)");
		$store = app('store');
		if($request->ajax())
		{
			$this->LogMsg("Process AJAX data");
			#
			# cart has user_id that is lgged in.
			#
			$payment_ref = $request->input("pr");
			$this->LogMsg("Payment Ref [".$payment_ref."]");
			$cart = Cart::find($id);
			$user = User::find($cart->user_id);
			$customer = Customer::where('customer_email',$user->email)->first();
			#
			# Cart Data has all the details of the purchase,
			# shipping and payment method entered in the Confirm Stage
			#
			$cartdata = CartData::where('cd_cart_id',$id)->first();
			#
			# Cart items has Product and QTY
			#
			$cartitems = CartItem::where('cart_id',$id)->get();

			$order = $this->CreateOrder($customer,$cartdata,$cartitems,$payment_ref);
			dispatch(new OrderPlaced($store,$customer->customer_email, $order));
			Mail::to($customer->customer_email)->send(new OrderPlacedEmail($store, $customer->customer_email, $order));
			if(sizeof($payment_ref)>0)
			{
				dispatch(new OrderPaid($store, $customer->customer_email, $order));
				Mail::to($customer->customer_email)->send(new OrderPaidEmail($store,$customer->customer_email,$order));
			}
	        return json_encode(array("S"=>"OK","C"=>$id,"CD"=>$cartdata->id,"CI"=>sizeof($cartitems),"Order"=>$order->id));
		}
		else
		{
	        return json_encode(array("S"=>"ERROR", "C"=>0));
		}
	}



	/**
	 * Call by AJAX method to Create an Order which may be paid or not.
	 *
	 * @param	App\Models\Customer	$customer
	 * @param	App\Models\CartData	$crtdata
	 * @param	App\Models\CartItem	$cartitems
	 * @param	string	$payment_ref
	 * @return	mixed
	 */
	protected function CreateOrder($customer,$cartdata,$cartitems,$payment_ref)
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
