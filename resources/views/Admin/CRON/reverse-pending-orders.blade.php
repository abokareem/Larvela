<?php
/**
 * \date 2017-09-20
 *
 * \addtogroup CRON
 * Iterate through the 'orders' table, get all rows that are on 'W" status and NOT PAID.
 * Check if they are older than 7 days, if so reverse the products back into stock.
 * Send an email to the customer, admin and close order off as cancelled.
 */
namespace App;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItems;
use App\Traits\Logger;
use DB;

use App\Jobs\OrderCancelled;



class CRON_ReversePendingOrders
{
use Logger;

	public function Run()
	{
		$this->setFileName("store-cron");
		$store = app('store');
		$one_week_ago = date("Y-m-d", strtotime("-1 week"));
		$pending_orders = Order::where('order_status','W')->get();
		if(sizeof($pending_orders)>0)
		{
			$this->LogStart();
			foreach($pending_orders as $o)
			{
				if($o->order_status == "W")
				{
					$this->LogMsg("Checking order ID [".$o->id."] Payment=".$o->order_payment_status." Placed [".$o->order_date."]");
					if($o->order_payment_status == "W")
					{
						$this->LogMsg("Pending order! -- Check Dates (placed ".$o->order_date.")");
						if($o->order_date == $one_week_ago)
						{
							$this->LogMsg("Order ".$o->id." matches criteria!");
							$customer = Customers::where('id',$o->order_cid)->first();
							$email = $customer->customer_email;
							$this->LogMsg("Customer email [".$email."]");

							#
							# Order matches, need to add all product back to stop and mark order items cancelled 
							# and then order as cancelled, set updated date
							#
							$order_items = OrderItems::where('order_item_oid',$o->id)->get();
							$this->LogMsg("Loaded ".sizeof($order_items)." items for Order");
							\DB::beginTransaction();
							foreach($order_items as $item)
							{
								#
								# add back the stock the user
								#
								$this->LogMsg("Reverse back stock level for [".$item->order_item_sku."]");
								$product = Product::where('prod_sku',$item->order_item_sku)->first();
								$current_stock_qty = $product->prod_qty;
								$new_qty = $item->order_item_qty_purchased + $current_stock_qty;
								$this->LogMsg("In stock [".$product->prod_qty."] add back [".$item->order_item_qty_purchased."]  new qty [".$new_qty."]");
								$product->prod_qty = $new_qty;
								$product->save();
								$item->order_item_dispatch_status = "C";
								$item->save();
							}
							$o->order_status = "C";
							$o->save();
							\DB::commit();
							$cmd = new OrderCancelled($store, $email, $o);
							dispatch($cmd);
							$this->LogMsg("TX Complete.");
						}
						else
						{
							$this->LogMsg("Skip -- Date is [".$o->order_date."] looking for ".$one_week_ago." ");
						}
					}
					else
					{
						$this->LogMsg("Skipping order ID ".$o->id.", Payment Status [".$o->order_payment_status."]");
					}
				}
			}
			$this->LogEnd();
		}
	}
}


$object = new CRON_ReversePendingOrders;
$object->Run();
