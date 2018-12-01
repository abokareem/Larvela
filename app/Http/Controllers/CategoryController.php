<?php
/**
 * \class	CategoryController
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-09-05
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
namespace App\Http\Controllers;


use Input;
use Session;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;


use App\Models\Store;
use App\Models\Category;
use App\Helpers\StoreHelper;
use App\Models\CategoryProduct;
use App\Services\CategoryService;


use App\Traits\Logger;


/**
 * \brief Controller code for all aspects of Category Handling
 *
 * 2016-09-22 Change table from categories to category
 */
class CategoryController extends Controller
{
use Logger;

	/**
	 * Show a Delete confirmation page, a category should not be deleted unles there 
	 * are no products associated with it.
	 *
	 * @param	integer $id Row id of category to be deleted
	 * @return 	mixed
	 */
	public function DeleteCategory($id)
	{

		$stores = $this->getStoreArray();
		$category = Category::find($id);
		$categories = Category::all();
		return view('Admin.Categories.deleteconfirm',[
			'category'=>$category,
			'categories'=>$categories,
			'stores'=>$stores,
			]);
	}



	/**
	 * Delete the selected category provided it has no children.
	 *
	 * @date 2016-09-05
	 * @route	POST ROUTE: /admin/category/delete/{id}
	 *
	 * @param	integer $id Row id of category to be deleted
	 * @return 	mixed
	 */
	public function DoDeleteCategory($id)
	{
		$deleted_rows = 0;
		$assigned_rows = CategoryProduct::where('category_id',$id)->get();

		$form = Input::all();
		if(array_key_exists('id',$form))
		{
			if($id == $form['id'])
			{
				$category = Category::find($id);
				if($category->category_parent_id > 0)
				{
					if(sizeof($assigned_rows) == 0)
					{
						$deleted_rows = Category::find($id)->delete();
						\Session::flash('flash_message',"Category removed!");
					}
					else
					{
						\Session::flash('flash_error',"ERROR - Category has products assigned - cannot delete!");
					}

				}
				else
				{
					if(Category::where('category_parent_id',$id)->count()==0)
					{
						if(sizeof($assigned_rows) == 0)
						{
							$deleted_rows = Category::find($id)->delete();
							\Session::flash('flash_message',"Category removed!");
						}
						else
						{
							\Session::flash('flash_error',"ERROR - The Category still has products assigned - cannot delete!");
						}
					}
					else
						\Session::flash('flash_error',"ERROR - Category has children assigned, delete them first!");
				}
			}
		}
		return $this->ShowCategories();
	}


	/**
	 * Show the defined categories (from the categories table).
	 *
	 * @date	2016-01-01
	 * @route	GET ROUTE: /admin/categories
	 *
	 * @return 	mixed
	 */
	public function ShowCategoriesPage(Request $request)
	{
		$store = app('store');
		$store_id =  $request->query('s',$store->id);
		$stores = Store::all();
		$categories = Category::where('category_store_id',$store_id)->get();
		return view('Admin.Categories.showcategories',[
			'store'=>$store,
			'stores'=>$stores,
			'store_id'=>$store_id,
			'categories'=>$categories
			]);
	}




	/**
	 * Return a View showing list of all categories.
	 *
	 * @return	mixed
	 */
	public function ShowCategories()
	{
		$store = app('store');
		$stores = Store::all();
		$categories = Category::all();
		return view('Admin.Categories.showcategories',[
			'store'=>$store,
			'stores'=>$stores,
			'store_id'=>0,
			'categories'=>$categories
			]);
	}





	/**
	 * Given a category ID, return an edit page.
	 *
	 * @date	2016-01-01
	 * @route	GET ROUTE: /admin/category/edit/{id}
	 *
	 * @param	integer	$id - row id of category from categories table.
	 * @return 	mixed
	 */
	public function ShowEditCategoryPage($id)
	{
		$cat = Category::find($id);
		$categories = Category::all();
		$stores = Store::all();
		$store = app('store');
		return view('Admin.Categories.editcategory',[
			'category'=>$cat,
			'categories'=>$categories,
			'store'=>$store,
			'stores'=>$stores
			]);
	}



	/**
	 * Return a view for adding a new category to the system.
	 *
	 * @route	GET ROUTE: /admin/category/addnew
	 * @date	2016-01-01
	 *
	 * @return 	mixed
	 */
	public function ShowAddCategoryPage()
	{
		$categories = Category::all();
		$stores = Store::all();
		$store = app('store');
		return view('Admin.Categories.addcategory',[
			'categories'=>$categories,
			'store'=>$store,
			'stores'=>$stores
			]);
	}



	/**
	 * Insert the new category definition into the categories table.
	 * Uses the servicerequest model to do the insert.
	 *
	 * @route	POST ROUTE: /admin/category/save
	 *
	 * @param	CategoryRequest	$request
	 * @return	mixed
	 */
	public function SaveNewCategory(CategoryRequest $request)
	{
		CategoryService::insert($request);	
		return $this->ShowCategories();
	}



	/**
	 * Update the DB with the revised category data and return as a list
	 *
	 * @date	2016-01-01
	 * @route	POST ROUTE: /admin/category/update/{id}
	 *
	 *
	 * @param	CategoryRequest	$request
	 * @param	integer	 $id Row id of category to be deleted
	 * @return	mixed
	 */
	public function UpdateCategory(CategoryRequest $request, $id)
	{
		$request['id'] = $id;
		CategoryService::update($request);	
		return $this->ShowCategories();
	}





	/**
	 * Return a 0 indexed array of stores
	 *
	 * @return	array
	 */
	protected function getStoreArray()
	{
		$list = array();
		$list[0]="Demo Store";
		$rows = Store::all();
		foreach($rows as $r)
		{
			$list[$r->id] = $r->store_name;
		}
		return $list;
	}
}
