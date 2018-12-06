<?php
/**
 * \class	CheckoutController
 * \date	2018-09-19
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version 1.0.3
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
namespace App\Http\Controllers;


use Request;
use App\Http\Requests;
use Redirect;
use Input;
use Auth;
use Session;

use App\Services\CartLocking;
use App\Services\ShippingFactory;
use App\Events\Larvela\ShowCartMessage;
use App\Events\Larvela\AddToCartMessage;

use App\User;
use App\Models\Cart;
use App\Models\Image;
use App\Models\Product;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CartData;
use App\Models\CartItem;
use App\Models\ProductLock;
use App\Models\ProdImageMap;
use App\Models\StoreSetting;
use App\Models\CustomerAddress;

use App\Traits\Logger;


/** 
 * \brief Shipping and Payment display logic (after Cart Logic)
 */
class CheckoutController extends Controller
{
use Logger;

private $user;

	/**
	 * Constuct a new cart and make sure we are authenticated before using it.
	 *
	 * @return	void
	 */ 
	public function __construct()
	{
		$this->middleware('auth');
		$this->setFileName("larvela");
		$this->setClassName("CheckoutController");
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
	 * Calculate the total weight of our cart items and the shipping options.
	 *
	 * @todo - Change shipping logic to possibly use Chain Of Responsilility pattern 
	 * to ask each shipping module for the shipping options, cost and displayable description and a form id value to pass back.
	 *
	 * cart ->SHIPPING -> confirm/payment -> purchased
	 *        --------
	 *
	 * NEED TO USE A SHIPPING FACTORY TO GET AND CALCULATE SHIPPING See: app\Services\Shipping
	 *
	 *
	 *
	 * Need to: Get user's details and return them, plus shipping options
	 * Next action will be confirm/pay screen
	 *
	 * @return	mixed
	 */
	public function Checkout()
	{
		$this->LogFunction("Checkout()");

		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$free_shipping = 0;
		$theme_path ="";
		
		$user = User::find(Auth::user()->id);
		$customer = Customer::where('customer_email',$user->email)->first();
		$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));
		$items = $cart->items;
		$options = array();
		$products = array();

		if(sizeof($items) > 0)
		{
			$products = array_map(function($item) { return Product::find($item['product_id']); }, $items->toArray());
			$free_shipping_module = ShippingFactory::getModuleByName("Free_Shipping");
			if(!is_null($free_shipping_module))
			{
				$options = $free_shipping_module->Calculate($store, $user, $products, $address);
			}
			#
			# $options will be NULL if there is no free shipping, in this case go and get
			# all modules and give each a chance at returning shipping options.
			#
			if(sizeof($options)==0)
			{
				$modules = ShippingFactory::getAvailableModules();
				$options = ShippingFactory::getShippingOptions($modules,$store, $user, $products, $address);

				$theme_path = \Config::get('THEME_CART')."2-shipping";
			}
			else
			{
				$theme_path = \Config::get('THEME_CART')."2-freeshipping";
			}
		}

		$quantities = array();
		$qtymap = array();
		$this->LogMsg("Iterate through cart - look for duplicate products");
		foreach($items as $item)
		{
			$this->LogMsg("Item ID: ".$item->id);
			if(in_array($item->product_id, $quantities)==true)
			{
				$qtymap[$item->product_id]++; 
				$this->LogMsg( "Item in cart already, inc QTY [".$qtymap[$item->product_id]."]" );
			}
			else
			{
				$qtymap[$item->product_id] = 1;
				$this->LogMsg( "Item added" );
				array_push( $quantities, $item->product_id);
			}
		}
		#$this->LogMsg("QTY Mappings ".print_r($qtymap,true));
		#
		# on exit from above qtymap[] has our unique product id's and the quantity of each.
		#
		$combine_codes = array();
		$product_id_list = array();
		$total = 0;
		$this->LogMsg("Iterate through cart - Add up duplicate products");
		foreach($items as $item)
		{
			if(in_array($item->product_id, $product_id_list)==true) continue;
			/*
			 * Save it in array so we skip it and get one entry in the shoping cart
			 */
			$this->LogMsg("Add to product_id_list");
			array_push($product_id_list,$item->product_id);
			$thumbnail = null;
			$product = Product::find($item->product_id);


			#
			# if any products have free shipping, flag it.
			# free_shipping == 0 means shipping is included.
			#
			if($product->prod_has_free_shipping != 0)
			{
				$free_shipping++;
			}

			array_push($combine_codes, $product['prod_combine_code']);

			$this->LogMsg("Product [".$product['prod_sku']."]" );
			$this->LogMsg("Inc qty");
			$product['qty'] = $qtymap[$item->product_id];
			$this->LogMsg("calc price retail [".$product->prod_retail_cost." * ".$qtymap[$item->product_id]." ]" );
			$sub_total = $product->prod_retail_cost * $qtymap[$item->product_id];
			
			$product['sub_total'] = $sub_total;
			$base_images = ProdImageMap::where('product_id',$item->product_id)->get();
			foreach($base_images as $bi)
			{
				$bimage = Image::find($bi->image_id);
				if($bimage->image_order == 0)
				{
					$thumbnails = $bimage->thumbnails()->get();
					foreach($thumbnails as $t)
					{
						if($t->image_order == 1)
						{
							$thumbnail = $t;
							break;
						}
					}
				}
			}
			if(is_object($thumbnail))
			{
				$product['thumbnail'] = $thumbnail->image_folder_name."/".$thumbnail->image_file_name;
			}
			else
			{
				$product['thumbnail'] = "media/product-image-missing.jpeg";
			}
			array_push($products, $product);
			$total += $sub_total;
		}
		$countries = Country::orderBy('country_name')->get();

		$this->LogMsg("View will be [".$theme_path."]");
		return view($theme_path,[
			'store'=>$store,
			'settings'=>$settings,
			'countries'=>$countries,
			'cart'=>$cart,
			'cart_data'=>$cart_data,
			'items'=>$items,
			'user'=>$user,
			'customer'=>$customer,
			'address'=>$address,
			'postal_options'=>$options
			]);
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
	 *
	 *
	 * cart ->shipping -> CONFIRM/PAYMENT -> purchased
	 *                    ---------------
	 *
	 * @return	mixed
	 */
	public function xxConfirm()
	{
		$this->LogFunction("Confirm()");

		$store = app('store');
		$form = \Input::all();
		$customer_id = $form['cid'];
		$customer = Customer::find($customer_id);
		$this->LogMsg("Customer ID [".$customer_id."]");
		$user = User::where('email', $customer->customer_email)->first();
		$address = CustomerAddress::where('customer_cid',$customer->id)->first();
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));
		$items = $cart->items;
		$products = array_map(function($item) {return( Product::find($item['product_id']) );}, $items->toArray());
		$products_out_of_stock = array_filter( $products, function($product){if($product['prod_qty'] == 0) return $product;});
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		if(sizeof($products_out_of_stock) > 0)
		{
			$THEME_ERRORS = \Config::get('THEME_ERRORS');
			$theme_path = $THEME_ERRORS."cart-item-out-of-stock";
			return view($theme_path,[ 'store'=>$store, 'setttings'=>$settings, 'products'=>$products_out_of_stock,'user'=>$user]);
		}

		$payment_method = $form['p'];
		$shipping_method= $form['s'];	# product ID of shipping method.. use to get cost
		$this->LogMsg("Payment Method [".$payment_method."]");
		$this->LogMsg("Shipping Method [".$shipping_method."]");
		
		$s_route = "-ship";
		$route = "";

		$this->LogMsg("Work out Payment Method - Route");

		#
		#  @todo Redesign to use route factory or call payment gateways to get the route for each gateway
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

		#
		# {FIX_2017-09-12} Return an error page if product is now out of stock
		#
#		if(sizeof($products_out_of_stock) > 0)
#		{
#			$theme_path = $THEME_ERRORS."cart-item-out-of-stock";
#			return view($theme_path,[ 'store'=>$store, 'products'=>$products_out_of_stock,'user'=>$user]);
#		}
		$this->LogMsg("Call LockProducts for cart ID [".$cart->id."]");
		
		$cartlocking = new CartLocking;
		$cartlocking->LockProducts($cart->id);

		$THEME_CART = \Config::get('THEME_CART');
		$theme_path = $THEME_CART.$route.$s_route;
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