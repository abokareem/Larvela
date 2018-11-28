<?php
/**
 * \class	CartOeprations
 * \date	2018-11-28
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
namespace App\Http\Controllers\Cart;

use Auth;
use Session;
use Request;
use Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Events\Larvela\ShowCartMessage;
use App\Events\Larvela\AddToCartMessage;


use App\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\CartData;
use App\Models\Customer;
use App\Models\StoreSetting;
use App\Models\CustomerAddress;
use App\Services\CartItemService;


use App\Traits\Logger;


/** 
 * \brief Cart Operations class contains methods for doing specific tasks on an active Cart.
 *
 */
class CartOperations extends Controller
{
use Logger;

private $user;

	/**
	 * Constuct a new cart and make sure we are authenticated before using it.
	 *
	 * @return	void
	 */ 
	public function __construct()
	{
		$this->middleware('auth');
		$this->setFileName("larvela");
		$this->setClassName("CartOperations");
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
	 * Add an item to the cart and redirect to the show cart page.
	 *
	 * @param	integer	$id		Product ID to add
	 * @return	mixed
	 */
	public function addItem(Request $request, $id)
	{
		$this->LogFunction("addItem()");
		CartItemService::Additem($request, $id);
		$store= app('store');
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		if(!is_null($cart))
		{
			$product = Product::find($id);
			$m = new AddToCartMessage($store,Auth::user(),$cart,$product);
			$m->dispatch();
		}
		return redirect('/cart');
	}



	/**
	 * Given the id of a cart_items product, remove it.
	 *
	 * @pre		User must be logged in.
	 * @param	integer	$id		ID of product to remove.
	 * @return	mixed
	 */
	public function removeItem($id)
	{
		$this->LogFunction("removeItem()");
		CartItemService::DeleteItem($id);
		return redirect('/cart');
	}



	/**
	 * Called from cart to inc the qty required
	 * Checks if there is enoguh stock to increment qty.
	 *
	 * @param	integer	$cid		carts id
	 * @param	integer	$iid		cart_items id
	 * @return	mixed
	 */
	public function incCartQty($cid, $iid)
	{
		$this->LogFunction("incCartQty($cid, $iid)");
		CartItemService::IncrementQty($cid, $iid);
		return Redirect::to("/cart");
	}



	/**
	 * Decrement the required item by 1 by removing it from the cart,
	 * if there are two or more, the reminaing duplicated rows == the qty in the cart.
	 *
	 * 2017-09-13 - Changed to use the new QTY column, dec that unless it already 1
	 *
	 * @param	integer	$cid
	 * @param	integer	$iid
	 * @return	mixed
	 */
	public function decCartQty($cid, $iid)
	{
		$this->LogFunction("decCartQty($cid, $iid)");
		CartItemService::DecrementQty($cid, $iid);
		return Redirect::to("/cart");
	}








	/**
	 *
	 * LOOK AT MOVING THIS OUT LATER
	 *
	 *
	 *
	 *
	 * Customer has sat on the cart confirm page for too long and the page has timedout
	 * Display a formatted error page inticating the page timed out waiting for the user to
	 * complete the action.
	 *
	 * Note: A background task will unlock the locked products the user has attempted to purchase.
	 *
	 *
	 * @return	mixed
	 */
	public function CartTimeoutError()
	{
		$this->LogFunction("CartTimeoutError()");

		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();

		$user = User::find(Auth::user()->id);
		$customer = Customer::where('customer_email', $user->email)->first();
		$address = CustomerAddress::where('customer_cid', $customer->id)->first();
		$cart = Cart::where('user_id',Auth::user()->id)->first();
		$cart_data = CartData::firstOrNew(array('cd_cart_id'=>$cart->id));

		$theme_path = \Config::get('THEME_ERRORS')."cart-timeout";
		return view($theme_path,[
			'store'=>$store,
			'settings'=>$settings,
			'cart'=>$cart,
			'cart_data'=>$cart_data,
			'user'=>$user,
			'customer'=>$customer,
			'address'=>$address]);
	}

}
