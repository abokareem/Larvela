<?php
/**
 * \class	AUPOST_Shipping
 * \date 	2018-08-24
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
namespace App\Services\Shipping;



use App\Models\Store;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CartItem;
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

		$total_weight = 0;
		$items = CartItem::where('cart_id',$cart->id)->get();
		foreach($items as $item)
		{
			$filtered_array = array_filter($products,function($p) use ($item){if($p->id==$item->product_id) return $p;});
			$product =array_shift( $filtered_array );
			$product_weight = $product->prod_weight * $item->qty;
			$total_weight += $product_weight;
		}
		$post_packs = Product::where('prod_combine_code',"AUPOST")->orderBy("prod_weight")->get()->toArray();
	
		$post_pack_product_array = array_filter($post_packs, function($postal_product) use ($total_weight)
			{static $stop_flag = false; if(($postal_product['prod_weight'] > $total_weight)&&(!$stop_flag)) {$stop_flag = true;return $postal_product['prod_weight'];} },$stop_flag=0);

		$post_pack_product = array_shift($post_pack_product_array);
		$pack_weight = $post_pack_product['prod_weight'];
		$postal_options = array();
		foreach($post_packs as $pp)
		{
			if($pp['prod_weight'] == $pack_weight)
			{
				array_push($postal_options, $pp);
			}
		}

		$code = "";
		foreach($postal_options as $po)
		{
			$option = new \stdClass;
			$option->id = $po['id'];
			$option->cost = $po['prod_retail_cost'];
			$option->display = $po['prod_title'];
			$option->html = "<input type='radio' name='shipping' value='".$this->MODULE_CODE."-".$po['id']."' >";
			$option->value=$this->MODULE_CODE."-".$po['id'];
			array_push($options, $option);
		}
		return $options;
	}





	/**
	 * Draft function to return if the module is active or not.
	 *
	 *
	 * @todo Need to change this to use a system variable or future shipping module admin page.
	 *
	 *
	 * @return	boolean
	 */
	public function isActive()
	{
		return true;
	}
}
