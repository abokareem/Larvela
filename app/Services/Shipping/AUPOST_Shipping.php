<?php
/**
 * \class	AUPOST_Shipping
 * \date 	2018-08-24
 * \version	1.0.2
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
namespace App\Services\Shipping;



use App\Models\Store;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Services\Shipping\IShippingModule;


/**
 * \brief Module to return Local Shipping options
 */
class AUPOST_Shipping implements IShippingModule
{

private $MODULE_CODE = "LARVELA_AUPOST";


	/**
	 * Return the unique module code <provider>_<shipping_type>
	 *
	 * @return	string
	 */
	public function getModuleCode()
	{
		return $this->MODULE_CODE;
	}



	/**
	 *
	 * @param	mixed	$store
	 * @param	mixed	$cart
	 * @param	mixed	$products
	 * @param	mixed	$customer_address
	 */
	public function Calculate($store, $cart, $products, $customer_address)
	{
		$options = array();

		$code = "";
		$post_packs = Product::where('prod_combine_code',"AUPOST")
			->orderBy("prod_weight")->get();

		foreach($post_packs as $p)
		{
			$option = new \stdClass;
			$option->cost = 0.0;
			$option->display = $p->prod_title;
#
# get the AUPOST products using the combine_code
#
			$option->value=$this->MODULE_CODE."-".$code;

			array_push($options, $option);
		}
		return $options;
	}


	public function isActive()
	{
		return true;
	}
}
