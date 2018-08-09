<?php
/**
 * \class	CartController
 * \date	2016-09-05
 * \author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;


use Request;
use App\Http\Requests;
use Redirect;
use Input;
use Auth;
use Session;

use App\Models\Cart;
use App\Models\CartData;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProdImageMap;
use App\Models\ProductLock;
use App\Models\CustomerAddress;
use App\Models\Image;
use App\Models\Users;



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
 */
class CartController extends Controller
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
		$this->setFileName("store");
		$this->LogStart();
		$this->LogMsg("CLASS::CartController");
	}


	/**
	 * Close of log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS::CartController");
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

		#
		# The logged in user
		#
		$user = Users::find(Auth::user()->id);
		#
		# The users customer data
		#
		$address = null;
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
			$this->LogMsg("Cart  ".print_r($cart, true));

			$cart_data = new CartData;
			$cart_data->cd_cart_id = $cart->id;
			$cart_data->save();
			$this->LogMsg("CartData cart ID [ $cart->id ]");
		}
		#
		# for any carts that dont have a matching cart_data row.
		#
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));
		#
		# uses Eloquent hasMany relationship to get cart_items with cart_id set to cart->id
		#
		$items = $cart->cartItems;

		/*
		 * map repeated products so qty value is correct.
		 */
		$quantities = array();
		$qtymap = array();
		$this->LogMsg("Iterate through cart - look for duplicate products");
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
		$this->LogMsg("Exit Loop.");
		#
		# products array is built to reflect the cart items, qty and sub totals.
		#
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
			$this->LogMsg("[".$item->product_id."] not in product_id_list array --- ADD!");
			array_push($product_id_list,$item->product_id);
			$thumbnail = null;
			$product = Product::find($item->product_id);

			$this->LogMsg("Item qty value is [".$item->qty."]");
			$product['qty'] = $item->qty;

			$this->LogMsg("calc price retail =[".$product->prod_retail_cost." * ".$qtymap[$item->product_id] );
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
						if($t->image_order==1)
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

		#
		# 2017-09-07 update the cart data table with sub-total
		#
		$cart_data->cd_sub_total = $total;
		$cart_data->save();

		$this->LogMsg("Done processing cart - now render view");

		$theme_path = \Config::get('THEME_CART')."1-cart";
		$store = app('store');
		return view($theme_path,[
			'store'=>$store,
			'cart'=>$cart,
			'cart_data'=>$cart_data,
			'user'=>$user,
			'customer'=>$customer,
			'address'=>$address,
			'products'=>$products,
			'items'=>$items,
			'total'=>$total,
			'tax'=>0,
			'shipping'=>0]);
	}


	/**
	 * Customer has sat on the cart confirm page for too long and the page has timedout
	 * Display a formatted error page inticating the page timed out waiting for the user to
	 * complete the action.
	 *
	 * Note: A background task will unlock the loked products the user has attempted to purchase.
	 *
	 *
	 * @return	mixed
	 */
	public function CartTimeoutError()
	{
		$this->LogFunction("CartTimeoutError()");

		$Customer = new Customer;

		$user = Users::find(Auth::user()->id);
		$customer = Customer::where('customer_email', $user->email)->first();
		$address = CustomerAddress::where('customer_cid', $customer->id)->first();
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));

		$theme_path = \Config::get('THEME_ERRORS')."cart-timeout";
		$store = app('store');
		return view($theme_path,[
			'store'=>$store,
			'cart'=>$cart,
			'cart_data'=>$cart_data,
			'user'=>$user,
			'customer'=>$customer,
			'address'=>$address]);
	}





	/**
	 * Calculate the total weight of our cart items and the shipping options.
	 *
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

		$Customer = new Customer;
		$CustomerAddress = new CustomerAddress;
		$Users = new Users;

		$Product = new Product;
		$Image = new Image;

		$store = app('store');
		$free_shipping = 0;
		#
		# The logged in user
		#
		$user = Users::find(Auth::user()->id);
		#
		# The users customer data
		#
		$customer = Customer::where('customer_email',$user->email)->first();
		$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));

		$this->LogMsg("Customer Data ".print_r($customer,true));
		$this->LogMsg("Address Data ".print_r($address,true));

		#
		# Cart, cart items and cart data
		#
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));
		$items = $cart->cartItems;

		$this->LogMsg("Cart Contents ".print_r($cart, true));
		$this->LogMsg("Cart ITEMS: ".print_r($items, true) );
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
		$this->LogMsg("QTY Mappings ".print_r($qtymap,true));
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
		##$post_packs = $Product->getByCombineCode("AUPOST");
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
		$this->LogMsg("Done processing cart - now render view");

		if($free_shipping == 0)
		{
			$theme_path = \Config::get('THEME_CART')."2-shipping";
			$this->LogMsg("View will be [".$theme_path."]");
			return view($theme_path,[
				'store'=>$store,
				'cart'=>$cart,
				'cart_data'=>$cart_data,
				'items'=>$items,
				'user'=>$user,
				'customer'=>$customer,
				'address'=>$address,
				'postal_options'=>$postal_options
				]);
		}
		else
		{
			$theme_path = \Config::get('THEME_CART')."2-freeshipping";
			$this->LogMsg("View will be [".$theme_path."]");
			return view($theme_path,[
				'store'=>$store,
				'cart'=>$cart,
				'cart_data'=>$cart_data,
				'items'=>$items,
				'user'=>$user,
				'customer'=>$customer,
				'address'=>$address,
				'postal_options'=>$postal_options
				]);
		}
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
	public function Confirm()
	{
		$this->LogFunction("Confirm()");
		$Product = new Product;
		$Customer = new Customer;
		$Users = new Users;
		$CustomerAddress = new CustomerAddress;

		$THEME_CART = \Config::get('THEME_CART');
		$THEME_ERRORS = \Config::get('THEME_ERRORS');

		$store = app('store');
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
		$user = Users::where('email', $customer->customer_email)->first();
		#
		# Cart, cart items and cart data
		#
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));
		$items = $cart->cartItems;
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
		$this->LockProducts($cart->id);
		$this->LogMsg("exit OK");

		return view($theme_path,[
			'store'=>$store,
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




	/**
	 * Called to put back into stock all the locked products for a specific cart.
	 *
	 * @param	integer	$cart_id
	 * @return	void
	 */
	public function ReverseProductLocks($cart_id)
	{
		$this->LogFunction("ReverseProductLocks( $cart_id )");
		
		$Product = new Product;
		$product_locks = ProductLock::where('product_lock_cid',$cart_id)->get();

		foreach($product_locks as $locked)
		{
			$this->LogMsg("Checking product_locks row ID [".$locked->id."]");
			$this->LogMsg("Product ID [".$locked->product_lock_pid."]");
			$product = Product::where('id',$locked->product_lock_pid)->first();
			$this->LogMsg("Checking Product Type for ID [".$product->id."]");
			if(($product->prod_type == 1)||($product->prod_type == 3))
			{
				$locked_qty = $locked->product_lock_qty;
				$stock_qty = $product->prod_qty;
				$this->LogMsg("Product QTY [".$stock_qty."] add back [".$locked_qty."]");
				\DB::beginTransaction();
				$this->LogMsg("Updating Product [".$product->id."]");
				$product->prod_qty = $stock_qty + $locked_qty;
				$product->save();
			}
			else
			{
				$this->LogMsg("Virtual Product - not stock levels to adjust");
			}
			$this->LogMsg("Removing Lock [".$locked->id."]");
			$locked->delete();
			\DB::commit();
		}
		$this->LogMsg("Reverse done Complete.");
	}



	/**
	 * Given the cart ID via an ajax call, update the time stamps and return OK
	 *
	 * @param	integer	$id		Cart ID to update
	 * @return	array
	 */
	public function UpdateLocks($id)
	{
		$this->LogFunction("UpdateLocks( $id )");
		$time = time();
		if(Request::ajax())
		{
			$this->LogMsg("Cart to update [".$id."]");
			$o = ProductLock::where('product_lock_cid',$id)->first();
			$o->product_lock_utime = $time;
			$cnt = $o->save();
			$data = array("S"=>"OK","C"=>$cnt);
	        return json_encode($data);
		}
		else
		{
			$data = array("S"=>"ERROR", "C"=>0);
	        return json_encode($data);
		}
	}




	/**
	 * Called to update the time stamp on the product locks to keep the product from being re-sold.
	 * return the number of rows found.
	 *
	 * @param	integer	$cart_id
	 * @return	integer
	 */
	public function UpdateProductLocks($cart_id)
	{
		$this->LogFunction("UpdateProductLocks( $cart_id )");
		
		
		$now = time();
		$this->LogMsg("Unix time is [".$now."]");
		$rows = ProductLock::where('product_lock_cid',$cart_id)->get();
		if(sizeof($rows) > 0)
		{
			$this->LogMsg("There are [".sizeof($rows)."] row items for Cart [".$cart_id."]");
			foreach($rows as $row)
			{
				$this->LogMsg("Uptime time for product lock row [".$row->id."]");
				$o = ProductLock::find($row->id);
				$o->product_lock_utime = $now;
				$rv = $o->save();
			}
			return sizeof($rows);
		}
		$this->LogMsg("No product locks to update!");
		return	0;
	}




	/**
	 * Add an item to the cart and redirect to the show cart page to 
	 * show the present items (if any).
	 * If qty is set in URL then add the item multiple times so cart reflect correctly.
	 * Creates a new cart if needed (initial add).
	 *
	 * @param	integer	$id		Product ID to add
	 * @return	mixed
	 */
	public function addItem(Request $request, $id)
	{
		$this->LogFunction("addItem()");
		$query = Request::input();
		$qty = 1;
		foreach($query as $key=>$val)
		{
			$qty = $key;
		}
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		if(!$cart)
		{
			$cart =  new Cart;
			$cart->user_id=Auth::user()->id;
			$cart->save();
		}
		$items = $cart->cartItems;	# uses Eloquent hasMany relationship to get cart_items with cart_id set to cart->id
		$this->LogMsg("Qty to save is [".$qty."]");

		foreach($items as $item)
		{
			if($item->product_id == $id)
			{
				$this->LogMsg("Item found - add [".$qty."] to existing qty of [".$item->qty."]");
				$item->qty = $item->qty+$qty;
				$item->save();
				return redirect('/cart');
			}
		}
		$this->LogMsg("Save single product to cart.");
		$cartItem  = new CartItem;
		$cartItem->product_id = $id;
		$cartItem->cart_id = $cart->id;
		$cartItem->qty = $qty;
		$cartItem->save();

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

		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$items = $cart->cartItems;
		foreach($items as $item)
		{
			if($id == $item->product_id)
			{
				CartItem::destroy($item->id);
			}
		}
		return redirect('/cart');
	}





	/**
	 * Given the cart ID, create the entries in the product_locks table to lock the products in the cart. 
	 * Decrement the store inventory by the amount so it reflects the correct values.
	 * If the customer has gone back in, reverse the product locks
	 * (put them back into stock and then do it all over again).
	 *
	 *
	 * @param	integer	$cart_id
	 * @return	mixed
	 */
	public function LockProducts($cart_id)
	{
		$this->LogFunction("LockProducts()");

		#
		# If the user comes back through the cart process then previous locks wil be present.
		# Update the timestamp to stop a simulateous CRON task from doing anything stupid.
		#
		$this->UpdateProductLocks($cart_id);
		$this->ReverseProductLocks($cart_id);

		$Product = new Product;

		$cartitems = CartItem::where('cart_id',$cart_id)->get();
		$this->LogMsg("There are [".sizeof($cartitems)."] rows in cart items");
		$product_list = array();
		#
		# for each item, get the product, we need the qty info later.
		#
		$this->LogMsg("Loop through cart items.");
		foreach($cartitems as $item)
		{
			$this->LogMsg("Fetching product [".$item->product_id."] with QTY of [".$item->qty."]");
			array_push($product_list, Product::where('id',$item->product_id)->first());
		}
		#
		# get existing rows for this cart
		#
		$this->LogMsg("Get any existing product locks.");
		$product_lock_rows = ProductLock::where('product_lock_cid',$cart_id)->get();
		$this->LogMsg("There are [".sizeof($product_lock_rows)."] rows locked");
		#
		# for each product, check for a row, if not there (newly added, insert it)
		# if its present, check
		$this->LogMsg("Iterate through products in cart.");

		foreach($cartitems as $item)
		{
			$o = new ProductLock();
			$o->product_lock_pid = $item->product_id;
			$o->product_lock_cid = $cart_id;
			$o->product_lock_qty = $item->qty;
			$o->product_lock_utime = time();
			$rv = $o->save();
			$this->LogMsg("Insert a new product lock for [".$item->product_id."] with a QTY of [".$item->qty."]");
#
# @todo check prod_type if basic or virtual (limited) then deduct from stock
# 
			$product = Product::find($item->product_id);
			$this->LogMsg("Product type [".$product->prod_type."]");
			if(($product->prod_type == 1)||($product->prod_type == 3))
			{
				$stock_qty = $product->prod_qty - $item->qty;
				$this->LogMsg("Product QTY was [".$product->prod_qty."] reduced to [".$stock_qty."]");
				$this->LogMsg("Updating Product [".$product->id."]");
				$product->prod_qty = $stock_qty;
				$product->save();
			}
			else
			{
				$this->LogMsg("Virtual Product - not stock to decrement");
			}
		}
		return;
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
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		if($cid == $cart->id)
		{
			$this->LogMsg("cart id [".$cid."]");
			$items = $cart->cartItems;
			$this->LogMsg("cart has [".sizeof($items)."] items");
			$cartqty=0;
			foreach($items as $item)
			{
				if($iid == $item->id) $cartqty++;
			}
			$this->LogMsg("There is [".$cartqty."] of the required item.");
			foreach($items as $item)
			{
				if($iid == $item->id)
				{
					$this->LogMsg("Incrementing QTY for row [".$item->id."]");
					$itemrow = CartItem::where('id',$item->id)->first();
					$product = Product::where('id',$item->product_id)->first();
					if($product->prod_qty >= $cartqty+1)
					{
						$itemrow->qty += 1;
						$itemrow->save();
					}
					else
					{
						$cartqty++;
						$this->LogMsg("Not enough stock in hand - have [".$product->prod_qty."] need [".$cartqty."]");
					}
				}
			}
			$this->LogMsg("Done");
		}
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
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		if($cid == $cart->id)
		{
			$items = $cart->cartItems;
			foreach($items as $item)
			{
				if($iid == $item->id)
				{
					$itemrow = CartItem::where('id',$item->id)->first();
					#
					# 2017-09-13 added code to use wty column
					#
					if($itemrow->qty == 1)
					{
						CartItem::where('id',$item->id)->delete();
					}
					else
					{
						$qty = $itemrow->qty-1;
						$itemrow->qty = $qty;
						$itemrow->save();
					}
					$this->LogMsg("item removed (1 less)");
				}
			}
			$this->LogMsg("Done");
		}
		return Redirect::to("/cart");
	}


}
