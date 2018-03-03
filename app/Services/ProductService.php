<?php
/**
 * \class	ProductService
 * @date	2016-08-01
 * @author	Sid Young <sid@off-grid-engineering.com>
 */
namespace App\Services;

use App\Http\Requests\ProductRequest;

use App\Models\Product;



/**
 * \brief Provides a service layer
 * called from the controller to insert or update the model using the request class
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
		
		$o->prod_date_created = date("Y-m-d");
		$o->prod_time_created = date("H:i:s");
		$o->prod_date_valid_from = date("Y-m-d");
		$o->prod_date_valid_to = date("Y-m-d");
		if($o->save() > 0)
		{
			\Session::flash('flash_message','Product Saved!');
			return $o->id;
		}
		else
		{
			\Session::flash('flash_error','Product failed to Save!');
		}
		return 0;
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
