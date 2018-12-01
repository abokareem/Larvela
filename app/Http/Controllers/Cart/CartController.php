<?php
/**
 * \class	CartController
 * \date	2016-09-05
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version 1.0.7
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
use App\Http\Controllers\Controller;

use App\Services\CartLocking;
use App\Services\ImageService;
use App\Events\Larvela\ShowCartMessage;
use App\Events\Larvela\AddToCartMessage;


use App\Models\Cart;
use App\Models\Product;
use App\Models\Image;
use App\Models\CartData;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\ProductLock;
use App\Models\ProdImageMap;
use App\Models\StoreSetting;
use App\Models\CustomerAddress;


use App\Services\CartItemService;


use App\User;
use App\Traits\Logger;


/** 
 * \brief Cart handling business logic.
 *
 * The CartController manages all aspects of the cart in the purchase flow.
 * Operations on the cart include adding/removing items, incrementing/decrementing quantities if possible.
 * Locking products at the "Confirm" stage and invoking order generation.
 *
 * {INFO_2017-09-07} Added cart_data to hold transient data between calls.
 * {FIX_2017-10-25} Refactored Customers to Customer references.
 * {INFO_2018-10-22} Moved Cart Item Operations to a Service Class
 */
class CartController extends Controller
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
		$this->setClassName("CartController");
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
	 * Show the users cart, if the user does not have a cart then create one.
	 * Create the Cart data object as well.
	 * Cart is displayed form the "products" array passed to the view.
	 *
	 * CART ->shipping -> confirm/payment -> purchased
	 * ----
	 *
	 *
	 * GET ROUTE: /cart
	 * 
	 * @pre		User must be logged in to get user id.
	 * @post	Cart created in DB
	 * @return	mixed
	 */
	public function ShowCart()
	{
		$this->LogFunction("ShowCart()");

		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$user = User::find(Auth::user()->id);
		$address = null;
		$thumbnails = array();
		$customer = Customer::where('customer_email',$user->email)->first();
		if(!is_null($customer))
		{
			$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));
		}
		else
		{
			return view('auth.register');
		}
		$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		if(!$cart)
		{
			$this->LogMsg("Create new cart for user");
			$cart = new Cart;
			$cart->user_id = Auth::user()->id;
			$cart->save();
			$this->LogMsg("Cart UID [ $cart->user_id ]");
			$cart = Cart::where('user_id',Auth::user()->id)->first();
			#$this->LogMsg("Cart  ".print_r($cart, true));

			$cart_data = new CartData;
			$cart_data->cd_cart_id = $cart->id;
			$cart_data->save();
			$this->LogMsg("CartData cart ID [ $cart->id ]");
		}
		#
		# for any carts that dont have a matching cart_data row.
		#
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));
		$items = $cart->items;

		/*
		 * map repeated products so qty value is correct.
		 */
		$quantities = array();
		$qtymap = array();
		$this->LogMsg("Iterate through cart - look for duplicate products");
		if(is_null($items) == false)
		{
			foreach($items as $item)
			{
				$this->LogMsg("Item: ".$item->id."  - Cart [".$item->cart_id."]  Product ID [".$item->product_id."] QTY [".$item->qty."]");
				if(in_array($item->product_id, $quantities)==true)
				{
					$qtymap[$item->product_id] = $item->qty; 
					$this->LogMsg( "Item in cart already, inc QTY [".$qtymap[$item->product_id]."]" );
				}
				else
				{
					$qtymap[$item->product_id] = $item->qty;
					array_push( $quantities, $item->product_id);
					$this->LogMsg( "Item added" );
				}
			}
		}
		$this->LogMsg("Exit Loop.");
		#
		# products array is built to reflect the cart items, qty and sub totals.
		#
		$product_id_list = array();
		$products = array();
		$total = 0;
		$this->LogMsg("Iterate through cart - Add up duplicate products");
		if(is_null($items)==false)
		{
			foreach($items as $item)
			{
				if(in_array($item->product_id, $product_id_list)==true) continue;
				/*
				 * Save it in array so we skip it and get one entry in the shoping cart
				 */
				$this->LogMsg("[".$item->product_id."] not in product_id_list array --- ADD!");
				array_push($product_id_list,$item->product_id);
				$product = Product::find($item->product_id);
	
				$thumbnails = ImageService::getThumbnails($product);
	
				$this->LogMsg("Item qty value is [".$item->qty."]");
				$product['qty'] = $item->qty;
	
				$this->LogMsg("calc price retail =[".$product->prod_retail_cost." * ".$qtymap[$item->product_id] );
				$sub_total = $product->prod_retail_cost * $qtymap[$item->product_id];
				
				$product['sub_total'] = $sub_total;
				if(is_array($thumbnails))
				{
					$product['thumbnail'] = $thumbnails[0]->image_folder_name."/".$thumbnails[0]->image_file_name;
				}
				if(sizeof($thumbnails)==0)
				{
					$product['thumbnail'] = "media/product-image-missing.jpeg";
				}
				array_push($products, $product);
				$total += $sub_total;
			}
		}

		#
		# 2017-09-07 update the cart data table with sub-total
		#
		$cart_data->cd_sub_total = $total;
		$cart_data->save();

		$m = new ShowCartMessage($store,$user,$cart,$cart_data);
		$m->dispatch();
		$this->LogMsg("Done processing cart - now render view");

		$theme_path = \Config::get('THEME_CART')."1-cart";
		return view($theme_path,[
			'store'=>$store,
			'settings'=>$settings,
			'cart'=>$cart,
			'cart_data'=>$cart_data,
			'user'=>$user,
			'customer'=>$customer,
			'address'=>$address,
			'products'=>$products,
			'thumbnails'=>$thumbnails,
			'items'=>$items,
			'total'=>$total,
			'tax'=>0,
			'shipping'=>0]);
	}



	/**
	 * Calculate the total weight of our cart items and the shipping options.
	 *
	 *
	 *======================================================================
	 * @todo - Change shipping logic to possibly use Chain Of Responsilility pattern 
	 * to ask each shipping module for the shipping options, cost and displayable
	 * description and a form id value to pass back.
	 *======================================================================
	 *
	 * cart ->SHIPPING -> confirm/payment -> purchased
	 *        --------
	 *
	 * Need to: Get user's details and return them, plus shipping options
	 * Next action will be confirm/pay screen
	 *
	 * @return	mixed
	 */
	public function ShowShipping()
	{
		$this->LogFunction("ShowShipping()");

		$User = new User;

		$Product = new Product;
		$Image = new Image;

		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$free_shipping = 0;
		#
		# The logged in user
		#
		$user = User::find(Auth::user()->id);
		#
		# The users customer data
		#
		$customer = Customer::where('customer_email',$user->email)->first();
		$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));

		#
		# Cart, cart items and cart data
		#
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));
		$items = $cart->items;
		$products = array();
		foreach($items as $item)
		{
			array_push($products, Product::find($item->product_id));
		}
		$product_with_free_shipping = array_filter($products,function($p) { return ($p->prod_has_free_shipping ==1) ? true:false; });
		if(sizeof($product_with_free_shipping) == sizeof($products)) $free_shipping = 1;
		$total_weight = array_reduce($products,function($weight,$p) { $weight += $p->prod_weight; echo "$weight <br>"; return $weight; }, $weight = 0);

		#$this->LogMsg("Cart Contents ".print_r($cart, true));
		#$this->LogMsg("Cart ITEMS: ".print_r($items, true) );
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
		$products = array();
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
		# We now have a list of product, the quantities
		# get 1 copy of each combine code so we can count up how many there are
		#
		$distinct_combine_code = array_unique($combine_codes);
		$this->LogMsg("Distinct combine code array [".print_r($distinct_combine_code,true)."]" );
		$cc_weight = array();
		$total_weight = 0;
		foreach($distinct_combine_code as $cc)
		{
			$this->LogMsg("Find all [".$cc."] products");
			$cc_weight[ $cc ] =0;
			foreach($products as $p)
			{
				if($p->prod_combine_code == $cc)
				{
					$this->LogMsg("Checking [".$p->prod_sku."]");
					$cnt = $qtymap[$p->id];
					$cc_weight[$cc] += ($p->prod_weight * $cnt);
					$total_weight += $cc_weight[$cc];
					$this->LogMsg("Count [".$cnt."]   Weight [".$p->prod_weight."]    Total so far [".$total_weight."]");
				}
			}
		}
		#
		# get all postal items sorted by weight, we need to find the
		# postal option that we can fit our items in
		#
		# Note: Currently fixed to AUPOST - make this a separate class with an interface
		#       we can hook into, factory to return a class based on configured delivery
		#       options.
		#
		$this->LogMsg("Postage items:");
		$post_packs = Product::where('prod_combine_code',"AUPOST")->orderBy("prod_weight")->get();

		$postal_options = array();
		$pack_weight = 0;
		foreach($post_packs as $pp)
		{
			$text = $pp->id." - ".$pp->prod_sku." - ".$pp->prod_short_desc." ".$pp->prod_weight."grams <br>";
			$this->LogMsg($text);
			if($pp->prod_weight > $total_weight)
			{
				$pack_weight = $pp->prod_weight;
				break;
			}
		}
		#
		# get all postal items that have the same weight
		# allows for express and regular in same weight.
		#
		$this->LogMsg("Postal Options are:");
		foreach($post_packs as $pp)
		{
			if($pp->prod_weight == $pack_weight)
			{
				array_push($postal_options, $pp);
				$this->LogMsg($pp->prod_sku." - ".$pp->prod_short_desc);
			}
		}
		if($free_shipping == 0)
		{
			$theme_path = \Config::get('THEME_CART')."2-shipping";
		}
		else
		{
			$theme_path = \Config::get('THEME_CART')."2-freeshipping";
		}
		$this->LogMsg("View will be [".$theme_path."]");
		return view($theme_path,[
			'store'=>$store,
			'settings'=>$settings,
			'cart'=>$cart,
			'cart_data'=>$cart_data,
			'items'=>$items,
			'user'=>$user,
			'customer'=>$customer,
			'address'=>$address,
			'postal_options'=>$postal_options
			]);
	}



	/**
	 * Customer has sat on the cart confirm page for too long and the page has timedout
	 * Display a formatted error page inticating the page timed out waiting for the user to
	 * complete the action.
	 *
	 * Note: A background task will unlock the locked products the user has attempted to purchase.
	 *
	 *
	 * @return	mixed
	 */
	public function CartTimeoutError()
	{
		$this->LogFunction("CartTimeoutError()");

		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();

		$user = User::find(Auth::user()->id);
		$customer = Customer::where('customer_email', $user->email)->first();
		$address = CustomerAddress::where('customer_cid', $customer->id)->first();
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));

		$theme_path = \Config::get('THEME_ERRORS')."cart-timeout";
		return view($theme_path,[
			'store'=>$store,
			'settings'=>$settings,
			'cart'=>$cart,
			'cart_data'=>$cart_data,
			'user'=>$user,
			'customer'=>$customer,
			'address'=>$address]);
	}




	/**
	 * Add an item to the cart and redirect to the show cart page.
	 *
	 * @param	integer	$id		Product ID to add
	 * @return	mixed
	 */
	public function addItem(Request $request, $id)
	{
		$this->LogFunction("addItem()");
		CartItemService::Additem($request, $id);
		$store= app('store');
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		if(!is_null($cart))
		{
			$product = Product::find($id);
			$m = new AddToCartMessage($store,Auth::user(),$cart,$product);
			$m->dispatch();
		}
		return redirect('/cart');
	}



	/**
	 * Given the id of a cart_items product, remove it.
	 *
	 * @pre		User must be logged in.
	 * @param	integer	$id		ID of product to remove.
	 * @return	mixed
	 */
	public function removeItem($id)
	{
		$this->LogFunction("removeItem()");
		CartItemService::DeleteItem($id);
		return redirect('/cart');
	}



	/**
	 * Called from cart to inc the qty required
	 * Checks if there is enoguh stock to increment qty.
	 *
	 * @param	integer	$cid		carts id
	 * @param	integer	$iid		cart_items id
	 * @return	mixed
	 */
	public function incCartQty($cid, $iid)
	{
		$this->LogFunction("incCartQty($cid, $iid)");
		CartItemService::IncrementQty($cid, $iid);
		return Redirect::to("/cart");
	}



	/**
	 * Decrement the required item by 1 by removing it from the cart,
	 * if there are two or more, the reminaing duplicated rows == the qty in the cart.
	 *
	 * 2017-09-13 - Changed to use the new QTY column, dec that unless it already 1
	 *
	 * @param	integer	$cid
	 * @param	integer	$iid
	 * @return	mixed
	 */
	public function decCartQty($cid, $iid)
	{
		$this->LogFunction("decCartQty($cid, $iid)");
		CartItemService::DecrementQty($cid, $iid);
		return Redirect::to("/cart");
	}
}
