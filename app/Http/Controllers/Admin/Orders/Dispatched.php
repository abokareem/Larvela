<?php
/**
 * @class	Dispatched
 * @author	Sid Young <sid@off-grif-engineering.com>
 * @date	2019-07-31
 * @version	1.0.0
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
namespace App\Http\Controllers\Admin\Orders;


use Auth;
use Input;
use Session;
use Request;
use Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Mail;

use App\Jobs\OrderCancelled;
use App\Jobs\OrderCompleted;
use App\Jobs\OrderDispatched;
use App\Jobs\OrderDispatchPending;

use App\Mail\OrderOnHoldEmail;
use App\Mail\OrderPendingEmail;
use App\Mail\OrderCancelledEmail;
use App\Mail\OrderCompletedEmail;
use App\Mail\OrderDispatchedEmail;

use App\User;
use App\Models\Store;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;

use App\Traits\Logger;


/**
 * \brief Admin related Order methods.
 * 
 * {INFO_2019-07-31} - refactored from AdminOrderController
 */
class Dispatched extends Controller
{
use Logger;


	/**
	 *------------------------------------------------------------
	 * Open log file
	 *------------------------------------------------------------
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-admin");
		$this->setClassName("Dispatched");
		$this->LogStart();
		$this->middleware(CheckAdmin::class);
	}


	/**
	 *------------------------------------------------------------
	 * Selected order has been marked by admin user as dispatched.
	 * - Update the DB record for the items supplied and backorder
	 * - Verify items supplied and mark as dispatched
	 * - if all supplied, mark order as dispatched and dispatch jobs
	 *------------------------------------------------------------
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
		return Redirect::to("/admin/orders");
	}
}
