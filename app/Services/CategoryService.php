<?php
/**
 * \class	CategoryService
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-21
 */
namespace App\Services;


use App\Models\Category;

use App\Http\Requests\CategoryRequest;


/**
 * \brief Service layer class for the Category model
 */
class CategoryService
{


	/**
	 * Insert a row into the categories table
	 *
	 * @param	CategoryRequest	$request	Request validation object
	 * @return	integer		row id
	 */
	public static function insert(CategoryRequest $request)
	{
		$rv = 0;
		$o = new Category;
		$o->category_title = $request['category_title'];
		$o->category_url = $request['category_url'];
		$o->category_description = $request['category_description'];
		$o->category_status = $request['category_status'];
		$o->category_visible = $request['category_visible'];
		$o->category_parent_id = $request['category_parent_id'];
		$o->category_store_id = $request['category_store_id'];
		if(($rv = $o->save()) > 0)
		{
			\Session::flash('flash_message','Category Saved!');
		}
		else
		{
			\Session::flash('flash_error','Category Save FAILED!');
		}
		return $rv;
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
