<?php
/**
 * \class	CheckPendingOrders
 * \date	2017-09-20
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version	1.0.0
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
 * \addtogroup CRON
 * Iterate through the 'orders' table, get all rows that are on 'W" status and NOT PAID.
 * Check if they are older than 7 days, if so reverse the products back into stock.
 * Send an email to the customer, admin and close order off as cancelled.
 */
namespace App\Jobs;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\Logger;
use App\Jobs\OrderCancelled;
use DB;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * \brief Iterate through the 'orders' table and get all rows that are on 'W" status and NOT PAID and check if they are older than 7 days.
 * - If older than 7 days then:
 * -- Reverse the products back into stock
 * -- Update the order as "cancelled".
 * To be added shortly:
 * -- Send an email to the customer,
 * -- Send an email to the store sales team 
 * -- Send an email to the system admin and
 */
class CheckPendingOrders implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;
use Logger;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job by calling the Run() method.
     *
     * @return void
     */
    public function handle()
    {
		$this->Run();
    }



    /**
     * Locate orders that are on waiting status and more than 7 days old.
     * Reverse the order.
	 *
	 * @todo inform the Customer and Sales team.
	 *
     * @return void
     */
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
							$customer = Customer::find($o->order_cid);
							$email = $customer->customer_email;
							$this->LogMsg("Customer email [".$email."]");

							#
							# Order matches, need to add all product back to stop and mark order items cancelled 
							# and then order as cancelled, set updated date
							#
							$order_items = OrderItem::where('order_item_oid',$o->id)->get();
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
							dispatch(new OrderCancelled($store, $email, $o));
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
