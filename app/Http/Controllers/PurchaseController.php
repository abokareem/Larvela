<?php
/**
 * \class	PurchaseController
 * \author	Sid Young <sid@off-grif-engineering.com>
 * \date	2017-01-10
 * \version	1.0.3
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


use PDF;
use Auth;
use Input;
use Session;
use Request;
use Redirect;
use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

use App\Jobs\OrderPaid;
use App\Jobs\OrderPlaced;
use App\Jobs\OrderCancelled;
use App\Jobs\OrderCompleted;
use App\Jobs\OrderDispatched;
use App\Jobs\OrderDispatchPending;

use App\Mail\OrderPaidEmail;
use App\Mail\OrderUnPaidEmail;
use App\Mail\OrderPlacedEmail;
use App\Mail\OrderOnHoldEmail;
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
 * \brief Purchase controller handles the after purchase business logic. OBSOLETE
 *
 * @todo - Need to move payment related code to factories and move admin order code
 * to elsewhere.
 */
class PurchaseController extends Controller
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
		$this->setClassName("PurchaseController");
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
	 * AJAX mthod to kick off the purchase cycle.
	 * - Implement relevant business logic for the product type.
	 *
#----------------------------------------------------------------------
#
# Purchase can be single item or multiple
# product types could be of different types, BASIC, VIRTUAL, PACK etc
#
#
#----------------------------------------------------------------------
	 * @param	integer	$id 		Cart ID
	 * @return	array
	 */
	public function Purchase($id)
	{
		$this->LogFunction("CartPaypalPurchase()");

		$store = app('store');
		$source = CustSource::where('cs_name', "WEBSTORE")->first();
		$customer_id = 0;
		$order_id = 0;
		$part2 = "";
		$part1 = "";
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

/*
*----------------------------------------------------------------------
*
* Need logic for PAID purchase and EFT Purchase
* Need to check for the product type and kick off a factory to call the purchase logic.
* Types to Handle are: BASIC, VIRTUAL, PACK.
* Virtual product has at least two type and they have different business logic
* cCart will
*----------------------------------------------------------------------
*/

		$order->order_dispatch_status = "W";
		$order->order_payment_status  = "W";

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
}
