<?php
/**
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-12-10
 * \version	1.0.1
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
 * \addtogroup Orders
 * CreateOrderItemsTrait - Creates Order items given the order.
 */
namespace App\Traits;
 
 
 
 
/**
 * \brief Create Order Items given the order and then fetching the locked Product data.
 */
trait CreateOrderItemsTrait 
{

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
	public function CreateOrderItems($order)
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
				$product = Product::find($p->product_lock_pid);
				if($product->prod_qty == 0)
				{
					$this->LogMsg("Out of stock product [".$p->product_lock_pid."]");
					dispatch(new OutOfStockJob($store, $store->store_sales_email, $product));
				}
				$OrderItem = new OrderItem;
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
}
