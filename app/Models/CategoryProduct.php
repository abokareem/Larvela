<?php
/**
 * \class	CategoryProduct
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
 * 
 *
 * Maps the Category a Product belongs to. A Product can map to several Categories.
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Pivot table for category_product mapping, was called prod_cat_maps
 *
 * {FIX_2016-09-22} Renamed table from prod_cat_maps to category_product
 */
class CategoryProduct extends Model
{


/**
 * Tell framework the table is singular pivot table
 * @var string $table
 */
public $table="category_product";



/**
 * Disable framework from timestamping rows
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * Array of columns that are mass assignable
 * @var array $fillable
 */
protected $fillable = array('category_id','product_id');



	/**
	 * Returns all relevant product_id rows
	 *
	 * @return  mixed
	 */
	public function products()
	{
		return $this->belongsTo('App\Models\Product');
	}
}
