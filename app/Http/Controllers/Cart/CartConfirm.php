<?php
/**
 * \class	CartConfirm
 * \date	2016-09-05
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version 1.0.6
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
namespace App\Http\Controllers\Cart;

use Auth;
use Input;
use Session;
use Request;
use Redirect;
use App\Http\Requests;
use App\Services\CartLocking;
use App\Http\Controllers\Controller;

use App\Events\Larvela\ShowCartMessage;
use App\Events\Larvela\AddToCartMessage;


use App\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartData;
use App\Models\Customer;
use App\Models\StoreSetting;
use App\Models\CustomerAddress;


use App\Traits\Logger;


/** 
 * \brief  The CartConfirm Controller manages the Confirmation process after payment details have been given.
 */
class CartConfirm extends Controller
{
use Logger;


	/**
	 * Constuct a new cart and make sure we are authenticated before using it.
	 *
	 * @return	void
	 */ 
	public function __construct()
	{
		$this->middleware('auth');
		$this->setFileName("larvela");
		$this->setClassName("CartConfirm");
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
	 * Route to the appropriate payment page to collect payment at/after this view renders.
	 * Confirm stage is after the shipping has been selected and the payment method, 
	 *
	 * Check and Lock products at this point... 5 minute time in CRON will ensure the products remain locked.
	 * If QTY is zero on a product, divert to an ERROR page.
	 *
	 * For COD and Bank payment, show an "Accept" page.
	 * For CC and PP payment, display text and request them to press the button.
	 *
	 * cart ->shipping -> payment/confirm -> purchased
	 *                    ---------------
	 *
	 * @return	mixed
	 */
	public function Confirm()
	{
		$this->LogFunction("Confirm()");
		$Customer = new Customer;
		$User = new User;
		$CustomerAddress = new CustomerAddress;

		$THEME_CART = \Config::get('THEME_CART');
		$THEME_ERRORS = \Config::get('THEME_ERRORS');

		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$this->LogMsg("Get Form Data.");
		$form = \Input::all();

		$payment_method = $form['p'];
		$shipping_method= $form['s'];	# product ID of shipping method.. use to get cost
		$customer_id = $form['cid'];

		$this->LogMsg("Payment Method [".$payment_method."]");
		$this->LogMsg("Shipping Method [".$shipping_method."]");
		$this->LogMsg("CID [".$customer_id."]");
		
		$customer = Customer::find($customer_id);
		$address = CustomerAddress::where('customer_cid',$customer->id)->first();
		$user = User::where('email', $customer->customer_email)->first();
		#
		# Cart, cart items and cart data
		#
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));
		$items = $cart->items;
		#
		# Build an array of products so we can pass descriptions to the required payment method if needed.
		#
		$products = array();	
		$products_out_of_stock = array();
		foreach($items as $item)
		{
			$product = Product::find($item->product_id);
			if($product->prod_qty == 0)
			{
				array_push($products_out_of_stock, $product);
			}
			array_push($products, $product);
		}
		$s_route = "-ship";
		$route = "";

		$this->LogMsg("Work out Payment Method - Route");

		#======================================================================
		#  @todo Redesign to use route factory or call payment gateways to get the route for each gateway
		#======================================================================
		#
		switch($payment_method)
		{
			case "0":
				$this->LogMsg("pay by COD");
				$route = "3-pay-by-cod";
				break;
			case "BD":
				$this->LogMsg("pay by EFT");
				$route = "3-pay-by-eft";
				break;
			case "PP":
				$this->LogMsg("pay by paypal selected");
				$route = "3-pay-by-pp";
				break;
			case "CC":
				$this->LogMsg("pay by Credit Card");
				$route = "3-pay-by-cc";
				break;
		}
		$cart_data->cd_payment_method = $payment_method;
		$cart_data->cd_shipping_method = $shipping_method;

		$product = Product::find($shipping_method);
		$shipping_cost = 0;
		if(!is_null($product))
		{
			$this->LogMsg("Found shipping product");
			$shipping_cost = $product->prod_retail_cost;
		}
		else
		{
			$this->LogMsg("Default to Pickup");
			$s_route="-pickup";	# no product so must be local pickup
		}
		$cart_data->cd_shipping = $shipping_cost;
		$cart_data->cd_total = $cart_data->cd_sub_total+$shipping_cost;
		$cart_data->save();

		#
		# temp order object
		#
		$order = new \stdClass;
		$order->order_number = substr(Session::getId(),0,8);

		$theme_path = $THEME_CART.$route.$s_route;
		#
		# {FIX_2017-09-12} Return an error page if product is now out of stock
		#
		if(sizeof($products_out_of_stock) > 0)
		{
			$theme_path = $THEME_ERRORS."cart-item-out-of-stock";
			return view($theme_path,[ 'store'=>$store, 'products'=>$products_out_of_stock,'user'=>$user]);
		}
		$this->LogMsg("Call LockProducts for cart ID [".$cart->id."]");
		
		$cartlocking = new CartLocking;
		$cartlocking->LockProducts($cart->id);

		$this->LogMsg("exit OK");

		return view($theme_path,[
			'store'=>$store,
			'setttings'=>$settings,
			'cart'=>$cart,
			'cart_data'=>$cart_data,
			'items'=>$items,
			'products'=>$products,
			'user'=>$user,
			'customer'=>$customer,
			'address'=>$address,
			'shipping'=>$product,	# the postage product being used.
			# @todo - add bank code when built
			#'bank_details'=>$bank_details,
			#
			'order'=>$order
			]);
	}
}
