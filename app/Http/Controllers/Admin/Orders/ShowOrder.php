<?php
/**
 * @class	ShowOrder
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


use PDF;
use Auth;
use Input;
use Session;
use Request;
use Redirect;
use App\Http\Requests;
use App\Http\Middleware\CheckAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;


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
 * \brief Order display class for administrator use only
 * 
 * {INFO_2018-08-22} - AdminOrderController refactored from OrderController
 */
class ShowOrder extends Controller
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
		$this->setClassName("AdminOrderController");
		$this->LogStart();
		$this->middleware(CheckAdmin::class);
	}



	/**
	 *------------------------------------------------------------
	 * Selected order has been marked by admin user as dispatched.
	 * dispatch any jobs that need to go.
	 *------------------------------------------------------------
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
}
