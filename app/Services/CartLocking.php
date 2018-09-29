<?php
/**
 * \class	CartLocking
 * \date	2018-08-14
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
 */
namespace App\Services;

use App\Models\Cart;
use App\Models\CartData;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductLock;


use App\Traits\Logger;


/** 
 * \brief Cart Lock handling logic.
 * Locking products at the "Confirm" stage and invoking order generation.
 */
class CartLocking
{
use Logger;

	/**
	 * Constuct a new cart and make sure we are authenticated before using it.
	 *
	 * @return	void
	 */ 
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("CartLocking");
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
	 * Called to put back into stock all the locked products for a specific cart.
	 *
	 * @param	integer	$cart_id
	 * @return	void
	 */
	public function ReverseProductLocks($cart_id)
	{
		$this->LogFunction("ReverseProductLocks( $cart_id )");
		
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

}
