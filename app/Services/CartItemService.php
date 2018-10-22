<?php
/**
 * \class	CartItemService
 * \date	2018-10-22
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version 1.0.0
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


use App\User;


/** 
 * \brief Cart Item handling services.
 */
class CartItemService
{



	/**
	 * Add an item to the cart and redirect to the show cart page to 
	 * show the present items (if any).
	 * If qty is set in URL then add the item multiple times so cart reflect correctly.
	 * Creates a new cart if needed (initial add).
	 *
	 * @pre		User must be logged in.
	 * @param	integer	$id		Product ID to add
	 * @return	void
	 */
	public function AddItem(Request $request, $id)
	{
		$store = app('store');
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
		$items = $cart->items;
		if(!is_null($items))
		{
			foreach($items as $item)
			{
				if($item->product_id == $id)
				{
					$item->qty = $item->qty+$qty;
					$item->save();
					return;
				}
			}
		}
		$product = Product::find($id);
		$cartItem  = new CartItem;
		$cartItem->product_id = $id;
		$cartItem->cart_id = $cart->id;
		$cartItem->qty = $qty;
		$cartItem->save();
	}



	/**
	 * Given the id of a cart_items product, remove it.
	 *
	 * @pre		User must be logged in.
	 * @param	integer	$id		ID of product to remove.
	 * @return	void
	 */
	public function DeleteItem($id)
	{
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$items = $cart->items;
		foreach($items as $item)
		{
			if($id == $item->product_id)
			{
				CartItem::destroy($item->id);
			}
		}
	}



	/**
	 * Called from cart to inc the qty required
	 * Checks if there is enoguh stock to increment qty.
	 *
	 * @param	integer	$cid		carts id
	 * @param	integer	$iid		cart_items id
	 * @return	void
	 */
	public static function IncrementQty($cid, $iid)
	{
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		if($cid == $cart->id)
		{
			$items = $cart->items;
			$cartqty=0;
			foreach($items as $item)
			{
				if($iid == $item->id) $cartqty++;
			}
			foreach($items as $item)
			{
				if($iid == $item->id)
				{
					$itemrow = CartItem::find($item->id);
					$product = Product::find($item->product_id);
					if($product->prod_qty >= $cartqty+1)
					{
						$itemrow->qty += 1;
						$itemrow->save();
					}
					else
					{
						$cartqty++;
					}
				}
			}
		}
	}



	/**
	 * Decrement the required item by 1 by removing it from the cart,
	 * if there are two or more, the reminaing duplicated rows == the qty in the cart.
	 *
	 * 2017-09-13 - Changed to use the new QTY column, dec that unless it already 1
	 *
	 * @param	integer	$cid
	 * @param	integer	$iid
	 * @return	void
	 */
	public static function DecrementQty($cid, $iid)
	{
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		if($cid == $cart->id)
		{
			$items = $cart->items;
			foreach($items as $item)
			{
				if($iid == $item->id)
				{
					$itemrow = CartItem::where('id',$item->id)->first();
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
		}
	}
}
