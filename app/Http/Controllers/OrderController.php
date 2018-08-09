<?php
/**
 * \class	OrderController
 * \author	Sid Young <sid@off-grif-engineering.com>
 * \date	2017-01-10
 *
 * 2017-10-07 Added PDF support for shop invoices.
 * 2017-10-13 Added PDF support for packing slips.
 *
 *
 */
namespace App\Http\Controllers;

use Request;
use App\Http\Requests;
use Redirect;
use Input;
use Auth;
use Session;

use PDF;

use App\Jobs\OrderPaid;
use App\Jobs\OrderCancelled;
use App\Jobs\OrderCompleted;
use App\Jobs\OrderPlaced;
use App\Jobs\OrderDispatched;
use App\Jobs\OrderDispatchPending;

use App\Mail\OrderPaidEmail;
use App\Mail\OrderUnPaidEmail;
use App\Mail\OrderCancelledEmail;
use App\Mail\OrderCompletedEmail;
use App\Mail\OrderPlacedEmail;
use App\Mail\OrderPendingEmail;
use App\Mail\OrderDispatchedEmail;
use App\Mail\OrderOnHoldEmail;


use App\Jobs\EmptyCartJob;
use App\Jobs\OutOfStockJob;

use App\Models\Store;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CustSource;
use App\Models\ProductLock;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartData;
use App\Models\OrderItem;

use App\Models\Users;
use App\Models\CustomerAddress;


use App\Traits\Logger;


/**
 * \brief Order controller handles instant, cart and delayed order creation.
 * Also has admin order page code.
 * 
 * 2017-09-21 Added order source, options include PAGE,CART, API, MANUAL etc
 * 
 * 
 */
class OrderController extends Controller
{
use Logger;


	/**
	 * Open log file
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->setFileName("orders");
		$this->LogStart();
		$this->LogMsg("CLASS:OrderController");
	}


	/**
	 * Close log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS:OrderController");
		$this->LogEnd();
	}





	/**
	 * Paypal Single product purchase made. Record paypal data.
	 *
	 * AJAX POST ROUTE: /instant/order/{id}
	 *
	 * @todo Move to Payment Gateway model and rename to InstantPayment(), call factory to get correct payment module
	 *
	 *
	 * Buy Now style product sales wont have a customer ID,
	 * need to extract email from the customer data returned by Paypal,
	 * check if email is known, create customer and back fill data to customers table.
	 *
	 * @param	integer	$id		Product ID from Purchase
	 * @return	mixed
	 */
	public function InstantPaypalPurchase($id)
	{
		$this->LogFunction("InstantPaypalPurchase()");

		$store = app('store');
		$product = Product::find($id);
		$source = CustSource::where('cs_name', "WEBSTORE")->first();

		$customer_id = 0;
		$order_id = 0;
		if(Request::ajax())
		{
			$data = null;
			$paypal = Input::all();
			foreach($paypal as $n=>$v)
			{
				$part1 = $n;
				foreach($v as $k=>$y)
				{
					$part2 = $k;
				}
				$str = $part1.$part2."]}}}";
				$data = json_decode($str,true,512);
			}
			$payment_ref = $this->extractPaymentRef($data);
			$email = $this->extractEmail($data);
			#
			# 2017-10-05 added ucwords to customer name.
			#
			$name  = ucwords($this->extractName($data));
			$order_status  = $this->extractStatus($data);
			$shipping_cost = $this->extractShipping($data);
			$shipping_method = "--";
			$order_total = $this->extractTotal($data);

			$order = new Order;
			$order->order_ref = $payment_ref;
			$order->order_src = "PAGE";
			$order->order_cart_id = 1;		# the default cart for the store admin
			$order->order_dispatch_status = "W";
			$order->order_payment_status  = "W";
			$order->order_shipping_value = $shipping_cost;
			$order->order_shipping_method = $shipping_method;
			$order->order_value = $order_total;
			$order->order_date = date("Y-m-d");
			$order->order_time = date("H:i:s");
			$order->order_status = "W";		# waiting to complete order (paid and dispatched)
			$order->order_payment_status = "P";

			$this->LogMsg("Paypal Status: [".$order_status."]");

			$this->LogMsg("Find customer: [".$email."]");
			$customer = Customer::where('customer_email',$email)->first();
			if($customer == null)
			{
				$this->LogMsg("Creating customer.");
				$Customer->customer_email = $email;
				$Customer->customer_name = $name;
				$Customer->customer_source_id = $source->id;
				$Customer->customer_store_id = $store->id;
				$this->LogMsg("Save customer: [".$email."]");
				$Customer->save();

				$address = $this->CreateCustomerAddress($data);
				$address->customer_cid = $Customer->id;
				$this->LogMsg("Save address: [".print_r($address, true)."]");
				$address->save();

				$this->LogMsg("Save customer: [".$email."]");
				$order->order_cid = $customer_id = $Customer->id;
			}
			else
			{
				$this->LogMsg("Using existing customer: [".$email."]");
				$order->order_cid = $customer_id = $customer->id;
				$address = CustomerAddress::where('customer_cid',$customer->id)->first();
				if(is_null($address))
				{
					$this->LogMsg("No existing customer address information!");
					$address = $this->CreateCustomerAddress($data);
					$address->customer_cid = $customer->id;
					$this->LogMsg("Save extracted address: [".print_r($address, true)."]");
	                $address->save();
				}
			}
			$this->LogMsg("Saving new Order for [".$email."]");
			$this->LogMsg("Order data [".print_r($order,true)."]");
			$order->save();

			$items = $this->extractItems($data);
			foreach($items as $item)
			{
				$this->LogMsg("Product found [".print_r($product,true)."]");
				$OrderItem = new OrderItem;
				$OrderItem->order_item_oid = $order->id;
				$OrderItem->order_item_sku = $product->prod_sku;
				$OrderItem->order_item_desc= $product->prod_title;
				$OrderItem->order_item_qty_purchased = $item['quantity'];
				$OrderItem->order_item_qty_supplied = 0;
				$OrderItem->order_item_qty_backorder = 0;
				$OrderItem->order_item_dispatch_status = "W";
				$OrderItem->order_item_price= $item['price'];
				$OrderItem->order_item_date = date("Y-m-d");
				$OrderItem->order_item_time = date("H:i:s");
				$OrderItem->save();
				$this->LogMsg("Order Item saved [".print_r($OrderItem,true)."]");

				$this->LogMsg("Decrement QTY by [".$OrderItem->order_item_qty_purchased."]");
				$the_product = Product::find($id);
				$the_product->decrement('prod_qty',$OrderItem->order_item_qty_purchased);
				$the_product->update(array(
					'prod_date_updated'=>date("Y-m-d"), 
					'prod_time_updated'=>date("H:i:s") 
					));
				if($the_product->prod_qty == 0)
				{
					$this->LogMsg("Dispatch out of stock notification.");
					$cmd = new OutOfStockJob($store, $store->store_sales_email, $product);
					dispatch($cmd);
				}
			}
			$this->LogMsg("Dispatching Order Job");
			dispatch(new OrderPlaced($store, $email, $order));
			$this->LogMsg("Dispatching Order Job - returning OK");
		}
		$data = array("S"=>"OK","OID"=>$order_id,"CID"=>$customer_id);
		$this->LogMsg("Done!");
		return json_encode($data);
	}



	/**
	 * Paypal product purchase made via Cart.
	 *
	 * AJAX POST ROUTE: /cart/order/{id}
	 *
	 *
	 * @todo Move to Payment Gateway model and rename to CartPurchase(), call factory to get correct payment module
	 *
	 *
	 * Using the Cart and the Logged in user details,
	 * construct an order and order items,
	 * email user and clear cart.
	 *
	 * @param	integer	$id	cart_id
	 * @return	string
	 */
	public function CartPaypalPurchase($id)
	{
		$this->LogFunction("CartPaypalPurchase()");

		$store = app('store');
		$source = CustSource::where('cs_name', "WEBSTORE")->first();
		$customer_id = 0;
		$order_id = 0;
		if(Request::ajax())
		{
			$data = null;
			$paypal = Input::all();
			foreach($paypal as $n=>$v)
			{
				$part1 = $n;
				foreach($v as $k=>$y)
				{
					$part2 = $k;
				}
				$str = $part1.$part2."]}}}";
				$data = json_decode($str,true,512);
			}
			#
			# {FIX_2018-02-19} Array not supported. in Logger Trait.
			#
			$this->LogMsg( print_r($data,true) );

			$payment_ref = $this->extractPaymentRef($data);
			$email = $this->extractEmail($data);
			$name  = ucwords($this->extractName($data));
			$this->LogMsg("Paypal eMail: [".$email."]");
			$this->LogMsg("Paypal Name:  [".$name."]");
			$order_status  = $this->extractStatus($data);
			$this->LogMsg("Paypal status:  [".$order_status."]");

			$cart = Cart::find($id);
			$cart_data = CartData::where('cd_cart_id',$id)->first();
	
			$order = new Order;
			$order->order_cart_id = $id;
			$order->order_ref = $payment_ref;
			$order->order_src = "CART";
			$order->order_dispatch_status = "W";
			$order->order_payment_status  = "W";
			$order->order_status = "W";		# waiting to complete order (paid and dispatched)

			$order->order_shipping_value = $cart_data->cd_shipping;
			$order->order_shipping_method = $cart_data->cd_shipping_method;
			$order->order_value = $cart_data->cd_total;

			$order->order_date = date("Y-m-d");
			$order->order_time = date("H:i:s");
			$order->order_payment_status = "P";
	
			$this->LogMsg("Find customer: [".$email."]");
			$customer = Customer::where('customer_email',$email)->first();
			if($customer == null)
			{
				$this->LogMsg("Creating customer.");
				$Customer->customer_email = $email;
				$Customer->customer_name = $name;
				$Customer->customer_source_id = $source->id;
				$Customer->customer_store_id = $store->id;
				$Customer->save();

				$this->LogMsg("Save customer: [".$email."]");
				$order->order_cid = $Customer->id;
				$customer_id = $Customer->id;
				$order->save();

				$this->LogMsg("Extracting address info");
				$address = $this->CreateCustomerAddress($data);
				$address->customer_cid = $Customer->id;
				$this->LogMsg("Update Address CID.");
				$address->save();
				$this->LogMsg("Saved!");
			}
			else
			{
				$this->LogMsg("Using existing customer: [".$email."]");
				$order->order_cid = $customer->id;
				$customer_id = $customer->id;
			}
			$this->LogMsg("Saving new Order for [".$email."]");
			$order->save();
			$this->LogMsg("Order Data [".print_r($order,true)."]");

			$this->CreateOrderItems($order);

			$this->LogMsg("Clearing Cart");
			$cmd = new EmptyCartJob($id);
			dispatch($cmd);

			$this->LogMsg("Dispatching Order Job");
			# $store, $email, $order
			dispatch(new OrderPlaced($store, $email, $order));

			$this->LogMsg("Dispatching Order Job - returning OK");
			Mail::to($email)->send(new OrderPlacedEmail($store, $email, $order));
		}
		$data = array("S"=>"OK","CARTID"=>$id,"OID"=>$order_id,"CID"=>$customer_id);
		$this->LogMsg("Done!");
		return json_encode($data);
	}




	/**
	 * Purchase made via COD, Bank Deposit or other delayed payment method.
	 *
	 * POST ROUTE: /payment/etf/{id}
	 *
	 * - Only callable from a Cart method (user is logged in).
	 * - Need to create a waiting order.
	 * - Dispatch Jobs indicating that the order is there but not yet paid.
	 * - Empty the cart as products are locked in the order.
	 *
	 * @param	integer	$id		Cart ID
	 * @return	mixed
	 */
	public function DelayedPurchase($id)
	{
		$this->LogFunction("DelayedPurchase()");

		$store = app('store');
		$user = Users::find(Auth::user()->id);
		$customer = Customer::where('customer_email', $user->email)->first();
		$this->LogMsg("Using existing customer: [".print_r($customer,true)."]");

		$payment_ref = "unknown";

		$cart = Cart::find($id);
		$count = CartData::where('cd_cart_id',$cart->id)->count();
		if($count == 0)
		$cart_data = new CartData();
		else
		$cart_data = CartData::where('cd_cart_id',$cart->id)->first();

		$order = new Order;
		$order->order_cart_id = $id;
		$order->order_ref = $payment_ref;
		$order->order_src = "CART";
		$order->order_dispatch_status = "W";
		$order->order_payment_status  = "W";
		$order->order_date = date("Y-m-d");
		$order->order_time = date("H:i:s");
		$order->order_cid = $customer->id;
		$order->order_shipping_value = $cart_data->cd_shipping;
		$order->order_shipping_method = $cart_data->cd_shipping_method;
		$order->order_value = $cart_data->cd_total;
		$this->LogMsg("Saving order now.");
		$order->save();

		$this->LogMsg("Find or add new customer");
		$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));
		
		$this->CreateOrderItems($order);

		$this->LogMsg("Dispatch Job -> EmptyCartJob");
		$cmd = new EmptyCartJob($id);
		dispatch($cmd);

		$this->LogMsg("Dispatch Job -> OrderDispatchPending");
		dispatch(new OrderDispatchPending($store, $user->email, $order));

		$this->LogMsg("Dispatch Email -> OrderPendingEmail");
		Mail::to($user->email)->send(new OrderPendingEmail($store, $user->email, $order));

		$this->LogMsg("Done");
		$theme_path = \Config::get('THEME_CART')."order_pending";
		return view($theme_path,['store'=>$store,'order'=>$order]);
	}






	/**
	 *============================================================
	 *
	 *                       DEVELOPMENT
	 *
	 *============================================================
	 *
	 *
	 * @return	mixed
	 */
	public function Purchase(Request $request)
	{
		$this->LogFunction("Purchase()");

		$theme_path = \Config::get('THEME_ERRORS')."invalid_payment_route";
		$form = \Input::all();

		switch($form['p'])
		{
			case "COD":
			case "EFT":
				return $this->DelayedPurchase($form['cid']);
				break;
			default:
				return view($theme_path);
		}
	}







	/**
	 * Retrieve the current orders and craft an array of objects containing the
	 * order data needed for the view.
	 *
	 * Current Orders are orders that are not completed, cancelled or marked as closed
	 *
	 * Current status values:
	 * ----------------------
	 * Waiting - In cart waiting for payment
	 * Paid - Paid but not dispatched
	 * Dispatched and finished
	 * Cancelled - either was waiting and cancelled by 7 day CRON or manually cancelled
	 *
	 * @return	mixed
	 */
	public function ShowCurrentOrders()
	{
		$this->LogFunction("ShowCurrentOrders()");

		$Order = new Order;

		$last_month = date("Y-m-d", strtotime("-2 month"));
		$today = date("Y-m-d");

		$recent_orders = Order::where('order_status','C')
			->whereBetween('order_date',array($last_month,$today))
			->orderBy('order_date')->get();

		$waiting_orders = Order::where('order_status','W')
			->orWhere('order_status','H')
			->orderBy('order_date')
			->orderBy('order_time')
			->get();
		$current_orders = $waiting_orders->merge($recent_orders);	
		$orders = array();
		foreach($current_orders as $o)
		{
			$customer = Customer::where('id',$o->order_cid)->first();

			$order_items = OrderItem::where('order_item_oid',$o->id)->get();
			$order_value = 0;
			if(is_null($o->order_value))
			{
				foreach($order_items as $oi)
				{
					$order_value += $oi->order_item_price;
				}
				$o->order_value = $order_value;
				$o->save();
			}
			$order = new \stdClass;
			$order->id = $o->id;
			$order->order_ref = $o->order_ref;
			$order->order_src = $o->order_src;
			$order->order_customer_name = $customer->customer_name;
			$order->order_customer_email = $customer->customer_email;
			$order->order_customer_mobile = $customer->customer_mobile;
			$order->order_status= $o->order_status;
			$order->order_value = $o->order_value;
			$order->order_shipping_value = $o->order_shipping_value;
			$order->order_shipping_method = $o->order_shipping_method;
			$order->order_payment_status = $o->order_payment_status;
			$order->order_dispatch_status = $o->order_dispatch_status;
			$order->order_item_count = sizeof($order_items);
			$order->order_date = $o->order_date;
			$order->order_time = $o->order_time;
			array_push($orders, $order);
		}


		$cancelled = Order::where('order_status','C')
			->whereBetween('order_date', array($last_month, $today))
			->orderBy('order_date')
			->orderBy('order_time')
			->get();
		$dispatched = Order::where('order_status','D')
			->whereBetween('order_date', array($last_month, $today))
			->orderBy('order_date')
			->orderBy('order_time')
			->get();

		return view('Admin.Orders.orders',[
			'orders'=>$orders,
			'orders_dispatched'=>$dispatched,
			'orders_cancelled'=>$cancelled
			]);
	}



	/**
	 * Selected order has been marked by admin user as dispatched.
	 * dispatch any jobs that need to go.
	 *
	 * GET ROUTE: /admin/order/view/{id}
	 *
	 */
	public function ShowOrder($id)
	{
		$this->LogFunction("ShowOrder()");
		$this->LogMsg("Order ID [".$id."]");

		$shipping_product = null;
		$order = Order::find($id);
		if(!is_null($order->order_shipping_method))
		{
			if( $order->order_shipping_method >0 )
			{
				$shipping_product = Product::find($order->order_shipping_method);
			}
		}
		$order_items = OrderItem::where('order_item_oid',$id)->get();

		$customer = Customer::find($order->order_cid);
		$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));

		return view("Admin.Orders.showorder",[
			'order'=>$order,
			'orderitems'=>$order_items,
			'customer'=>$customer,
			'address'=>$address,
			'shipping'=>$shipping_product
			]);
	}



	/**
	 *============================================================
	 *
	 *                       DEVELOPMENT
	 *
	 *============================================================
	 *
	 * Back Order an item given the itemID 
	 *
	 * GET ROUTE: /admin/order/boitem/{id}
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function BackOrderAnItem($id)
	{
		$this->LogFunction("BackOrderAnItem()");
		$this->LogMsg("Item ID [".$id."]");

		$order_item = OrderItem::where('id',$id)->first();
		$qty_purchased = $order_item->order_item_qty_purchased;
		$qty_supplied = $order_item->order_item_qty_supplied;
		$order_item->order_item_qty_backordered = $qty_purchased-$qty_supplied;
		$order_item->save();
		return $this->ShowOrder($order_item->order_item_oid);
	}




	/**
	 *============================================================
	 *
	 *                       TESTING
	 *
	 *============================================================
	 *
	 * Selected order has been marked by admin user as dispatched.
	 * - Update the DB record for the items supplied and backorder
	 * - Verify items supplied and mark as dispatched
	 * - if all supplied, mark order as dispatched and dispatch jobs
	 *
	 * POST ROUTE: /admin/order/dispatch/{order_id}
	 *
	 * @param	integer	$id	The order id
	 * @return	mixed
	 */
	public function OrderDispatched($id)
	{
		$this->LogFunction("OrderDispatched()");
		$this->LogMsg("Order ID [".$id."]");

		$form = Input::all();
		foreach($form as $k=>$v)
		{
			$supplied_qty = 0;
			$backorder_qty = 0;
			if($k=="_token") continue;
			$parts = explode("-",$k);
			if(sizeof($parts) == 2)
			{
				$order_item = OrderItem::where('id',$parts[1])->first();
				switch($parts[0])
				{
					case "su":
						$order_item->order_item_qty_supplied = $v;
						$order_item->save();
						break;
					case "bo":
						$order_item->order_item_qty_backorder = $v;
						$order_item->save();
						break;
				}
			}
		}

		$store = app('store');
		$order = Order::where('id',$id)->first();
		$customer = Customer::where('id',$order->order_cid)->first();
		$order_items = OrderItem::where('order_item_oid',$order->id)->get();
		$item_count = sizeof($order_items);
		#
		# Check each order item, if supplied == ordered then mark as dispatch
		#
		$items_dispatched = 0;
		$this->LogMsg("Order has ".$item_count." item(s).");
		foreach($order_items as $oi)
		{
			$this->LogMsg("Checking item [".$oi->id."]");
			if($oi->order_item_qty_purchased == $oi->order_item_qty_supplied)
			{
				$this->LogMsg("Setting item [".$oi->id."] to status D");
				$oi->order_item_dispatch_status = "D";
				$items_dispatched++;
				$oi->save();
			}
			else
			{
				$this->LogMsg("Skipping item [".$oi->id."] supplied(".$oi->order_item_qty_supplied.") != ordered(".$oi->order_item_qty_purchased.").");
			}
			$this->LogMsg("Processed ".$items_dispatched." of ".$item_count." items.");
		}
		if($items_dispatched == $item_count)
		{
			$this->LogMsg("Marking order [".$order->id."] as DISPATCHED.");
			$order->order_dispatch_status = "D";
			$order->order_status = "C";
			$order->order_dispatch_date = date("Y-m-d");
			$order->order_dispatch_time = date("H:i:s");
			$order->save();
			dispatch(new OrderCompleted($store, $customer->customer_email, $order));
			Mail::to($customer->customer_email)->send(new OrderCompletedEmail($store, $customer->customer_email, $order));
		}
		$this->LogMsg("Dispatch Job -> OrderDispatched()");
		dispatch(new OrderDispatched($store, $customer->customer_email, $order));

		$this->LogMsg("Send email using OrderDispatchedEmail");
		Mail::to($customer->customer_email)->send(new OrderDispatchedEmail($store, $customer->customer_email, $order));

		return $this->ShowCurrentOrders();
	}




	/**
	 *============================================================
	 *
	 *                       DEVELOPMENT
	 *
	 *============================================================
	 *
	 *
	 * @param	integer	$id		The order ID
	 * @return	mixed
	 */
	public function DispayPDFPackingSlip($id)
	{
		$this->LogFunction("DispayPDFPackingSlip()");
		$this->LogMsg("Order ID [".$id."]");

		$store = app('store');
		$shipping_product = null;
		$order = Order::where('id',$id)->first();
		if(!is_null($order->order_shipping_method))
		{
			if( $order->order_shipping_method >0 )
			{
				$shipping_product = Product::where('id', $order->order_shipping_method)->first();
			}
		}
		$order_items = OrderItem::where('order_item_oid',$id)->get();
		$customer = Customer::where('id',$order->order_cid)->first();
		$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));
		$pdf = \PDF::loadView('Admin.Orders.pdf-packingslip',[
			'order'=>$order,
			'store'=>$store,
			'orderitems'=>$order_items,
			'customer'=>$customer,
			'address'=>$address,
			'shipping'=>$shipping_product
			]);
		return $pdf->inline();
	}





	/**
	 *============================================================
	 *
	 *                       DEVELOPMENT
	 *
	 *============================================================
	 *
	 * Display a show invocie PDF of the selected order.
	 * Shop invoice is for shop to keep an account for a product being sold.
	 *
	 * @param	integer	$id		The order ID
	 * @return	mixed
	 */
	public function DispayPDFShopInvoice($id)
	{
		$this->LogFunction("DispayPDFShopInvoice()");
		$this->LogMsg("Order ID [".$id."]");

		$shipping_product = null;
		$order = Order::where('id',$id)->first();
		if(!is_null($order->order_shipping_method))
		{
			if( $order->order_shipping_method >0 )
			{
				$shipping_product = Product::where('id', $order->order_shipping_method)->first();
			}
		}
		$order_items = OrderItem::where('order_item_oid',$id)->get();

		$customer = Customer::where('id',$order->order_cid)->first();
		$address = CustomerAddress::firstOrNew(array('customer_cid'=>$customer->id));
		$pdf = \PDF::loadView('Admin.Orders.pdf-showorder',[
			'order'=>$order,
			'orderitems'=>$order_items,
			'customer'=>$customer,
			'address'=>$address,
			'shipping'=>$shipping_product
			]);
		return $pdf->inline();
	}



	/** 
	 * given the paypal data, extract the payment "state" value.
	 *
	 * MOVE TO FACTORY CLASS
	 *
	 * @param	array	$data
	 * @return	string
	 */
	protected function extractPaymentRef($data)
	{
		return $data['id'];
	}


	/** 
	 * given the paypal data, extract the payment "state" value.
	 *
	 * MOVE TO FACTORY CLASS
	 *
	 * @param	array	$data
	 * @return	string
	 */
	protected function extractStatus($data)
	{
		return $data['state'];
	}


	protected function extractShipping($data)
	{
		return $data['transactions']['amount']['details']['shipping'];
	}


	protected function extractTotal($data)
	{
		return $data['transactions']['amount']['total'];
	}



	/** 
	 * given the paypal data, extract the email address field and return it.
	 *
	 * MOVE TO FACTORY CLASS
	 *
	 * @param	array	$data
	 * @return	string
	 */
	protected function extractEmail($data)
	{
		return $data['payer']['payer_info']['email'];
	}



	/** 
	 * given the paypal data, extract the customers names and return it.
	 *
	 * MOVE TO FACTORY CLASS
	 *
	 * @param	array	$data
	 * @return	string
	 */
	public function extractName($data)
	{
		$s1 = $data['payer']['payer_info']['first_name'];
		$s2 = $data['payer']['payer_info']['last_name'];
		return $s1." ".$s2;
	}



	/** 
	 * Given the paypal data, extract the purchased items in this transaction.
	 *
	 * MOVE TO FACTORY CLASS
	 *
	 * @param	array	$data
	 * @return	string
	 */
	public function extractitems($data)
	{
		return $data['transactions']['item_list']['items'];
	}



	/** 
	 * Given the paypal data, extract the address info 
	 *
	 * MOVE TO FACTORY CLASS
	 *
	 * @param	array	$data
	 * @return	object
	 */
	protected function CreateCustomerAddress($data)
	{
		$address = new CustomerAddress;
		$address->customer_email =    $data['payer']['payer_info']['email'];
		$address->customer_address =  $data['payer']['payer_info']['shipping_address']['line1'];
		$address->customer_suburb =   $data['payer']['payer_info']['shipping_address']['city'];
		$address->customer_postcode = $data['payer']['payer_info']['shipping_address']['postal_code'];
		$address->customer_city  =    $data['payer']['payer_info']['shipping_address']['city'];
		$address->customer_state =    $data['payer']['payer_info']['shipping_address']['state'];
		$address->customer_country =  $data['payer']['payer_info']['shipping_address']['country_code'];
		$address->customer_status = "A";
		$address->customer_date_created = date("Y-m-d");
		$address->customer_date_updated = date("Y-m-d");
		$address->save();
		return $address;
	}



	/**
	 * Get the rows from the product locks table for this cart.
	 * Need to:
	 * - Get the relevant products
	 * - Save as an Order item and
	 * - remove the locks table entries.
	 *
	 * @param	mixed	$order
	 * @return	integer	Number of rows inserted
	 */
	protected function CreateOrderItems($order)
	{
		$this->LogFunction("CreateOrderItems()");

		$cart_id = $order->order_cart_id;
		$product_locks  = ProductLock::where('product_lock_cid',$cart_id)->get();
		$store = app('store');
		
		$item_count = sizeof($product_locks);
		if($item_count > 0)
		{
			$this->LogMsg("There are [".$item_count."] row items for Cart [".$cart_id."]");
			foreach($product_locks as $p)
			{
				$product = Product::where('id', $p->product_lock_pid)->first();
				if($product->prod_qty == 0)
				{
					$this->LogMsg("Out of stock product [".$p->product_lock_pid."]");
					$cmd = new OutOfStockJob($store, $store->store_sales_email, $product);
					dispatch($cmd);
				}
				$OrderItem = new OrderItems;
				$OrderItem->order_item_oid = $order->id;
				$OrderItem->order_item_sku = $product->prod_sku;
				$OrderItem->order_item_desc= $product->prod_title;
				$OrderItem->order_item_qty_purchased = $p->product_lock_qty;
				$OrderItem->order_item_qty_supplied = 0;
				$OrderItem->order_item_qty_backorder = 0;
				$OrderItem->order_item_dispatch_status = "W";
				$OrderItem->order_item_price= $product->prod_retail_cost;
				$OrderItem->order_item_date = date("Y-m-d");
				$OrderItem->order_item_time = date("H:i:s");
				$OrderItem->save();
				$p->delete();
			}
			return $item_count;
		}
		$this->LogMsg("Product Lock table entries are gone!");
		return 0;
	}



/*
MariaDB [rdstore]> desc orders;
+-----------------------+------------------+------+-----+------------+----------------+
| Field                 | Type             | Null | Key | Default    | Extra          |
+-----------------------+------------------+------+-----+------------+----------------+
| id                    | int(10) unsigned | NO   | PRI | NULL       | auto_increment |
| order_ref             | varchar(128)     | YES  |     | NULL       |                |
| order_src             | varchar(8)       | YES  |     | NULL       |                |
| order_cart_id         | int(10) unsigned | NO   | MUL | NULL       |                |
| order_cid             | int(10) unsigned | NO   | MUL | NULL       |                |
| order_status          | char(2)          | NO   |     | W          |                |
| order_shipping_method | varchar(32)      | YES  |     | NULL       |                |
| order_shipping_value  | decimal(13,2)    | YES  |     | NULL       |                |
| order_value           | decimal(13,2)    | YES  |     | NULL       |                |
| order_payment_status  | char(1)          | NO   |     | W          |                |
| order_dispatch_status | char(1)          | NO   |     | W          |                |
| order_date            | date             | NO   |     | 0000-00-00 |                |
| order_time            | time             | NO   |     | 00:00:00   |                |
+-----------------------+------------------+------+-----+------------+----------------+
13 rows in set (0.00 sec)
*/

	/**
	 * Mark the order as paid
	 *
	 * GET ROUTE: /admin/order/update/paid/{id}
	 *
	 * @param	integer	$id		Order ID
	 * @return	void
	 */
	public function MarkOrderPaid($id)
	{
		$this->LogFunction("MarkOrderPaid()");
		$this->LogMsg("Order ID [".$id."]");

		$order = Order::where('id',$id)->first();
		$order->order_payment_status = 'P';
		$order->save();
		$customer = Customer::where('id',$order->order_cid)->first();
		$store =app('store');
		$this->LogMsg("Dispatching Job -> OrderPaid");
		$cmd = new OrderPaid($store, $customer->customer_email, $order);
		dispatch($cmd);
		return $this->ShowCurrentOrders();
	}



	/**
	 * Mark the order as Unpaid in casae of a bounce
	 *
	 * GET ROUTE: /admin/order/update/unpaid/{id}
	 *
	 * @param	integer	$id		Order ID
	 * @return	void
	 */
	public function MarkOrderUnPaid($id)
	{
		$this->LogFunction("MarkOrderUnPaid()");
		$this->LogMsg("Order ID [".$id."]");

		$order = Order::where('id',$id)->first();
		$order->order_payment_status = 'W';
		$order->save();
		return $this->ShowCurrentOrders();
	}



	/**
	 * Mark the order as "On Hold" to stop reversal CRON jobs
	 *
	 * GET ROUTE: /admin/order/update/onhold/{id}
	 *
	 * @param	integer	$id		Order ID
	 * @return	void
	 */
	public function MarkOrderOnHold($id)
	{
		$this->LogFunction("MarkOrderOnHold()");
		$this->LogMsg("Order ID [".$id."]");

		$order = Order::where('id',$id)->first();
		$order->order_status = 'H';
		$order->save();

		# @todo Dispatch a Job called OrderPlacedOnHold to allow more external processing
		return $this->ShowCurrentOrders();
	}



	/**
	 * Put the order bck to waiting
	 *
	 * GET ROUTE: /admin/order/update/waiting/{id}
	 *
	 * @param	integer	$id		Order ID
	 * @return	void
	 */
	public function MarkOrderAsWaiting($id)
	{
		$this->LogFunction("MarkOrderAsWaiting()");
		$this->LogMsg("Order ID [".$id."]");

		$order = Order::where('id',$id)->first();
		$order->order_status = 'W';
		$order->save();
		return $this->ShowCurrentOrders();
	}



	/**
	 * Mark the order as cancelled
	 *
	 * GET ROUTE: /admin/order/update/cancel/{id}
	 *
	 * @param	integer	$id		Order ID
	 * @return	void
	 */
	public function MarkOrderAsCancelled($id)
	{
		$this->LogFunction("MarkOrderAsCancelled()");
		$this->LogMsg("Order ID [".$id."]");

		$order = Order::where('id',$id)->first();
		$order->order_status = 'C';
		$order->save();
		$customer = Customer::where('id',$order->order_cid)->first();
		$store =app('store');
		$cmd = new OrderCancelled($store, $customer->customer_email, $order);
		dispatch($cmd);
		return $this->ShowCurrentOrders();
	}
}
