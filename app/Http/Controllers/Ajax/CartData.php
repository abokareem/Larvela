<?php
/**
 * \class	CartData
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-28
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
namespace App\Http\Controllers\Ajax;


use Auth;
use Input;
use App\Http\Requests;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Request;

use App\User;
use App\Models\Cart;
use App\Models\Store;
use App\Models\Product;
use App\Http\Controllers\Controller;

use App\Traits\Logger;

/**
 * \brief Returns JSON data with the cart items and value
 * In a system with no Administration sub-system, this Controller is required. 
 */
class CartData extends Controller
{
use Logger;



	/**
	 * Setup logging
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-ajax");
		$this->setClassName("CartData");
		$this->LogStart();
	}



	/**
	 * Close off log
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}



	/**
	 * AJAX REQUEST -  Count number of items in cart and totalize the cost, return it as JSON data.
	 *
	 * Returns "C" for count of items in cart and "V" for the value of the items (Formatted)
	 *
	 * @return string JSON data with status code
	 */
	public function GetCartData()
	{
		$this->LogFunction("GetCartData()");

		$c = 0;
		$v = 0;
		if(Request::ajax())
		{
			$this->LogMsg("AJAX request OK");
			if(Auth::check())
			{
				$this->LogMsg("Auth check OK");
				$cart = Cart::where('user_id',Auth::user()->id)->first();
				if($cart)
				{
					$this->LogMsg("cart oK");
					$items = $cart->items;
					if(!is_null($items))
					{
						$this->LogMsg("cart has [".sizeof($items)."] items");
						foreach($items as $item)
						{
							$product = Product::find($item->product_id);
							$this->LogMsg("Item [".$item->id."]  PID [".$item->product_id."]  QTY [".$item->qty."] SKU [".$product->prod_sku."]  Price [".$product->prod_retail_cost."]");
							$v += $product->prod_retail_cost*$item->qty;
							$c += $item->qty;
						}
						$data = array('c'=>$c,'v'=>number_format($v,2));
						return json_encode($data);
					}
				}
			}
		}
		$data = array('c'=>0,'v'=>0);
		return json_encode($data);
	}
}
