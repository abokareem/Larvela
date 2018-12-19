<?php
/**
 * \class	TestEvents
 *
 * \addtogroup Testing
 * TestCart - Provides various manual test routines for triggering Larvela Events
 */
namespace App\Http\Controllers\Test;

use Auth;
use Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartData;
use App\Models\CartItem;
use App\Events\Larvela\ShowCartMessage;
use App\Events\Larvela\AddToCartMessage;
use App\Events\Larvela\PlaceOrderMessage;


/**
 * \brief Larvela Event testing code
 */
class TestEvents extends Controller
{



/*============================================================
 *
 *
 *                          CART 
 *
 *
 *============================================================
 */

	public function test_show_cart_message()
	{
		$store = app('store');
		$cart = Cart::first();
		$user = User::find($cart->user_id);
		$cart_data = CartData::where('cd_cart_id',$cart->id)->first();
		(new ShowCartMessage($store,$user,$cart,$cart_data))->dispatch();
		dd($cart_data);
	}



	public function test_add_to_cart_message()
	{
		$store = app('store');
		$cart = Cart::first();
		$cartitem = CartItem::where('cart_id',$cart->id)->first();
		$product = Product::find($cartitem->product_id);
		$user = User::find($cart->user_id);
		(new AddToCartMessage($store, $user, $cart, $product))->dispatch();
		dd(new AddToCartMessage($store, $user, $cart, $product));
	}



	public function test_place_order_message()
	{
		$store = app('store');
		$cart = Cart::first();
		$cart_data = CartData::where('cd_cart_id',$cart->id)->first();
		$order = Order::where('order_cart_id',$cart->id)->first();
		$user = User::find($cart->user_id);
		(new PlaceOrderMessage($store, $cart, $cart_data,  $user))->dispatch();
		dd(new PlaceOrderMessage($store, $cart, $cart_data, $user));
	}


}
