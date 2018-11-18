<?php
/**
 * \class	CategoryService
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-07-21
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
namespace App\Services;


use App\Models\Category;
use App\Models\CategoryProduct;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\CategoryRequest;


/**
 * \brief Service layer class for the Category model
 */
class CategoryService
{


	/**
	 * Given a list of categories, assign the product to them.
	 *
	 * @pre		Product should not already be assigned to category
	 *
	 * @param	Request	$request
	 * @param	integer	$product_id
	 * @return	void
	 */
	public static function AssignCategories(ProductRequest $request, $product_id)
	{
		$empty = array(1);
		$categories = $request->input('categories',$empty);
		if(sizeof($categories)>0)
		{
			foreach($categories as $c)
			{
				$o = new CategoryProduct;
				$o->category_id = $c;
				$o->product_id = $product_id;
				$o->save();
				echo "<h1> C=".$c." P=".$product_id."</h1>";
			}
		}
	}




	/**
	 * Insert a row into the categories table
	 *
	 MariaDB [rdstore]> desc category;
	 +----------------------+------------------+------+-----+---------+----------------+
	 | Field                | Type             | Null | Key | Default | Extra          |
	 +----------------------+------------------+------+-----+---------+----------------+
	 | id                   | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
	 | category_url         | varchar(255)     | NO   |     | NULL    |                |
	 | category_title       | varchar(255)     | NO   |     | NULL    |                |
	 | category_description | varchar(255)     | NO   |     | NULL    |                |
	 | category_parent_id   | int(10) unsigned | YES  |     | NULL    |                |
	 | category_status      | varchar(1)       | YES  |     | NULL    |                |
	 | category_visible     | varchar(1)       | YES  |     | NULL    |                |
	 | category_store_id    | int(10) unsigned | NO   |     | 0       |                |
	 +----------------------+------------------+------+-----+---------+----------------+
	 8 rows in set (0.00 sec)

	 * @param	CategoryRequest	$request	Request validation object
	 * @return	integer		row id
	 */
	public static function insert(CategoryRequest $request)
	{
		$o = new Category;
		$o->category_title = $request['category_title'];
		$o->category_url = $request['category_url'];
		$o->category_description = $request['category_description'];
		$o->category_status = $request['category_status'];
		$o->category_visible = $request['category_visible'];
		$o->category_parent_id = $request['category_parent_id'];
		$o->category_store_id = $request['category_store_id'];
		if($o->save() > 0)
		{
			\Session::flash('flash_message','Category Saved!');
		}
		else
		{
			\Session::flash('flash_error','Category Save FAILED!');
		}
		return $o->id;
	}



	/**
	 * Update an existing row given the Request object.
	 *
	 * @param	CategoryRequest	$request	Request validation object
	 * @return	integer	row id
	 */
	public static function update(CategoryRequest $request)
	{
		$rv = 0;
		$o = Category::find( $request['id'] );
		$o->category_title = $request['category_title'];
		$o->category_url = $request['category_url'];
		$o->category_description = $request['category_description'];
		$o->category_status = $request['category_status'];
		$o->category_visible = $request['category_visible'];
		$o->category_parent_id = $request['category_parent_id'];
		$o->category_store_id = $request['category_store_id'];
		if(($rv = $o->save()) > 0)
		{
			\Session::flash('flash_message','Category updated successfully!');
		}
		else
		{
			\Session::flash('flash_error','Category Update FAILED!');
		}
		return $rv;
	}
}
