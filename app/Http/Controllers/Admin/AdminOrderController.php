<?php
/**
 * \class	AdminOrderController
 * \author	Sid Young <sid@off-grif-engineering.com>
 * \date	2018-08-22
 * \version	1.0.2
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
 */
namespace App\Http\Controllers\Admin;


use PDF;
use Auth;
use Input;
use Session;
use Request;
use Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

use App\Jobs\OrderPaid;
use App\Jobs\OrderPlaced;
use App\Jobs\OrderCancelled;
use App\Jobs\OrderCompleted;
use App\Jobs\OrderDispatched;
use App\Jobs\OrderDispatchPending;

use App\Mail\OrderPaidEmail;
use App\Mail\OrderUnPaidEmail;
use App\Mail\OrderOnHoldEmail;
use App\Mail\OrderPlacedEmail;
use App\Mail\OrderPendingEmail;
use App\Mail\OrderCancelledEmail;
use App\Mail\OrderCompletedEmail;
use App\Mail\OrderDispatchedEmail;

use App\Jobs\EmptyCartJob;
use App\Jobs\OutOfStockJob;

use App\User;
use App\Models\Cart;
use App\Models\Store;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CartItem;
use App\Models\CartData;
use App\Models\OrderItem;
use App\Models\CustSource;
use App\Models\ProductLock;
use App\Models\CustomerAddress;

use App\Traits\Logger;


/**
 * \brief Admin related Order methods.
 * 
 * {INFO_2018-08-22} - AdminOrderController refactored from OrderController
 */
class AdminOrderController extends Controller
{
use Logger;


	/**
	 * Open log file
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-admin");
		$this->setClassName("AdminOrderController");
		$this->LogStart();
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
