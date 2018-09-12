<?php
/**
 * \class	Product
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-07-29
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
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide all CRUD functionality for the "products" table.
 */
class Product extends Model
{

/**
 * The table this model uses
 * @var string $table
 */
protected $table = "products";


/**
 * The table this model uses
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * columns that are mass-assignable
 * @var string $fillable
 */
protected $fillable = ['prod_sku','prod_title','prod_short_desc','prod_long_desc','prod_visible','prod_weight','prod_qty','prod_reorder_qty','prod_base_cost','prod_retail_cost','prod_combine_code','prod_date_created','prod_time_created','prod_date_updated','prod_time_updated','prod_date_valid_from','prod_date_valid_to','prod_type'];



	/**
	 * Get all images from the image_product pivot table
	 * returns an Eloquent Collection Object
	 *
	 * Usage:  $product->images()->get();
	 *
	 * @return	mixed
	 */
	public function images()
	{
		return $this->belongsToMany('App\Models\Image');
	}



	public function categories()
	{
		return $this->belongsToMany('App\Models\Category');
	}


	public function attributes()
	{
		return $this->belongsToMany('App\Models\Attribute');
	}

	
	/**
	 * Given a text string containing codes, and a producte object containg row data
	 * return a text string with the codes translated into data.
	 *
	 * @deprecated Do not use, being removed due to Mailable interface being implemented.
	 *
	 *
	 * @param	string	$text
	 * @param	mixed	$obj
	 * @return	string
	 */
	public function translate($text,$obj)
	{
		$translations = array( 
			"{PRODUCT_ID}"=>$obj->id,
			"{PRODUCT_SKU}"=>$obj->prod_sku,
			"{PRODUCT_TITLE}"=>$obj->prod_title,
			"{PRODUCT_NAME}"=>$obj->prod_title,
			"{PRODUCT_SHORT_DESC}"=>$obj->prod_short_desc,
			"{PRODUCT_LONG_DESC}"=>$obj->prod_long_desc,
			"{PRODUCT_WEIGHT}"=>$obj->prod_weight,
			"{PRODUCT_QTY}"=>$obj->prod_qty,
			"{PRODUCT_REORDER_QTY}"=>$obj->prod_reorder_qty,
			"{PRODUCT_BASE_COST}"=>$obj->prod_base_cost,
			"{PRODUCT_RETAIL_COST}"=>$obj->prod_retail_cost,
			"{PRODUCT_COMBINE_CODE}"=>$obj->prod_combine_code,
			"{PRODUCT_DATE_CREATED}"=>$obj->prod_date_created,
			"{PRODUCT_DATE_UPDATED}"=>$obj->prod_date_updated,
			"{PRODUCT_TIME_CREATED}"=>$obj->prod_time_created,
			"{PRODUCT_TIME_UPDATED}"=>$obj->prod_time_updated,
			"{PRODUCT_DATE_VALID_FROM}"=>$obj->prod_date_valid_from,
			"{PRODUCT_DATE_VALID_TO}"=>$obj->prod_date_valid_to
			);
		return str_replace(array_keys($translations), array_values($translations), $text);
	}
}
