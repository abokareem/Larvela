<?php
/**
 * \class	CategoryController
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-09-05
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Requests;
use Session;
use Input;


use App\Http\Requests\CategoryRequest;

use App\Services\CategoryService;
use App\Helpers\StoreHelper;

use App\Models\Store;
use App\Models\Category;
use App\Models\CategoryProduct;




/**
 * \brief Controller code for all aspects of Category Handling
 *
 * 2016-09-22 Cahnge table from categories to category
 */
class CategoryController extends Controller
{

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
		$store_id = $store->id;

		$query = $request->input();
		foreach($query as $n=>$v)
		{
			if($n=="s") $store_id = $v;
		}
		$stores = Store::all();
		$cats = Category::where('category_store_id',$store_id)->get();
		return view('Admin.Categories.showcategories',[
			'categories'=>$cats,
			'stores'=>$stores,
			'store_id'=>$store_id
			]);
	}




	/**
	 * Return a View showing list of all categories.
	 *
	 * @return	mixed
	 */
	public function ShowCategories()
	{
		$stores = Store::all();
		$categories = Category::all();
		return view('Admin.Categories.showcategories',[
			'categories'=>$categories,
			'stores'=>$stores,
			'store_id'=>0
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
		$Category = new Category;
		$Store = new Store;

		$cat = Category::find($id);
		$html_select_list = $Category->getSelectList("category_parent_id",$cat->category_parent_id);
		$store_select_list = $Store->getSelectList('category_store_id', $cat->category_store_id, true);

		return view('Admin.Categories.editcategory',['category'=>$cat,'html_select_list'=>$html_select_list, 'stores'=>$store_select_list]);
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
		$Category = new Category;
		$Store = new Store;

		$html_select_list = $Category->getSelectList("category_parent_id",0);
		$store_select_list = $Store->getSelectList('category_store_id', 0, true);
		
		return view('Admin.Categories.addcategory',['html_select_list'=>$html_select_list, 'stores'=>$store_select_list]);
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
