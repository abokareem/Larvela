<?php
/**
 * \class	ProductService
 * \date	2016-08-01
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version	1.0.4
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

use App\Http\Requests\ProductRequest;

use App\Models\Product;



/**
 * \brief Provides insert/update service for Products
 * - Called from the controller with a request class.
 */
class ProductService
{

	public static function insertArray( $request)
	{
		$rv = 0;
		$o = new Product;
		$o->prod_sku = $request['prod_sku'];
		$o->prod_title = $request['prod_title'];
		$o->prod_short_desc = $request['prod_short_desc'];
		$o->prod_long_desc = $request['prod_long_desc'];
		$o->prod_visible = $request['prod_visible'];
		$o->prod_weight = $request['prod_weight'];
		$o->prod_qty = $request['prod_qty'];
		$o->prod_reorder_qty = $request['prod_reorder_qty'];
		$o->prod_base_cost = $request['prod_base_cost'];
		$o->prod_retail_cost = $request['prod_retail_cost'];
		$o->prod_combine_code = $request['prod_combine_code'];
		$o->prod_type = $request['prod_type'];
		$o->prod_has_free_shipping = $request['prod_has_free_shipping'];
		$o->prod_status = "A";

		$o->prod_is_taxable = $request['prod_is_taxable'];
		$o->prod_tax_rate = $request['prod_tax_rate'];

		
		$o->prod_date_created = date("Y-m-d");
		$o->prod_time_created = date("H:i:s");
		$o->prod_date_valid_from = date("Y-m-d");
		$o->prod_date_valid_to = date("Y-m-d");
		if(($o->save()) >0)
		{
			\Session::flash('flash_message','Product Saved!');
			return $o->id;
		}
		else
		{
			\Session::flash('flash_error','Product failed to Save!');
			return 0;
		}
	}





	/**
	 * Insert a new row and return the row id or 0 on error.
	 *
	 * @param	ProductRequest	Request object
	 * @return	integer
	 */
	public static function insert(ProductRequest $request)
	{
		$o = new Product;
		$o->prod_sku = $request['prod_sku'];
		$o->prod_title = $request['prod_title'];
		$o->prod_short_desc = $request['prod_short_desc'];
		$o->prod_long_desc = $request['prod_long_desc'];
		$o->prod_visible = $request['prod_visible'];
		$o->prod_weight = $request['prod_weight'];
		$o->prod_qty = $request['prod_qty'];
		$o->prod_reorder_qty = $request['prod_reorder_qty'];
		$o->prod_base_cost = $request['prod_base_cost'];
		$o->prod_retail_cost = $request['prod_retail_cost'];
		$o->prod_combine_code = $request['prod_combine_code'];
		$o->prod_type = $request['prod_type'];
		$o->prod_has_free_shipping = $request['prod_has_free_shipping'];
		$o->prod_status = "A";
		
		$o->prod_is_taxable = $request['prod_is_taxable'];
		$o->prod_tax_rate = $request['prod_tax_rate'];

		$o->prod_date_created = date("Y-m-d");
		$o->prod_time_created = date("H:i:s");
		$o->prod_date_valid_from = date("Y-m-d");
		$o->prod_date_valid_to = date("Y-m-d");
		if($o->save() > 0)
		{
			\Session::flash('flash_message','Product Saved!');
		}
		else
		{
			\Session::flash('flash_error','Product failed to Save!');
		}
		return $o->id;
	}




	/**
	 * Update the model using the request data and return the number of rows affected.
	 *
	 * @param	ProductRequest	Request object
	 * @return	integer
	 */
	public static function update(ProductRequest $request)
	{
		$rv = 0;
		$o = Product::find($request['id']);
		$o->prod_sku = $request['prod_sku'];
		$o->prod_title = $request['prod_title'];
		$o->prod_short_desc = $request['prod_short_desc'];
		$o->prod_long_desc = $request['prod_long_desc'];
		$o->prod_visible = $request['prod_visible'];
		$o->prod_weight = $request['prod_weight'];
		$o->prod_qty = $request['prod_qty'];
		$o->prod_reorder_qty = $request['prod_reorder_qty'];
		$o->prod_base_cost = $request['prod_base_cost'];
		$o->prod_retail_cost = $request['prod_retail_cost'];
		$o->prod_combine_code = $request['prod_combine_code'];
		$o->prod_type = $request['prod_type'];
		$o->prod_has_free_shipping = $request['prod_has_free_shipping'];
		$o->prod_is_taxable = $request['prod_is_taxable'];
		$o->prod_tax_rate = $request['prod_tax_rate'];
		$o->prod_date_updated = date("Y-m-d");
		$o->prod_time_updated = date("H:i:s");
		if(($rv=$o->save()) >0)
		{
			\Session::flash('flash_message','Product updated successfully!');
		}
		else
		{
			\Session::flash('flash_error','Product updated FAILED!');
		}
		return $rv;
	}
}
