<?php
/**
 * \class	AdminProductController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-09-22
 * \version	1.0.5
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
namespace App\Http\Controllers\Admin;

use Redirect;
use Input;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;

use App\Services\ProductService;
use App\Services\Products\ProductPageFactory;

use App\Jobs\BackInStock;
use App\Jobs\ResizeImages;
use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;

use App\Models\Image;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductType;
use App\Models\Notification;
use App\Models\ProdImageMap;
use App\Models\CategoryProduct;
use App\Models\AttributeProduct;

use App\Traits\Logger;

/**
 * \brief MVC Controller to Handle the Product Administration functions initiated from the admin dashboard.
 * - Not specific to a type of product (avoid specific calls, use factories where possible).
 *
 * {INFO_2018-03-01} Removed all code except copy and product add 
 * {INFO_2018-03-04} Coded up Parent product view call
 * {INFO_2018-09-24} Refactored various calls so just core admin functions are present.
 */
class AdminProductController extends Controller
{
use Logger;


	/**
	 * Open log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-admin");
		$this->setClassName("AdminProductController");
		$this->LogStart();
	}
	
	/**
	 * Close log file
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}



	/**
	 * Return an administration view listing the selected products.
	 *
	 * GET ROUTE: /admin/products
	 *
	 * @param	Request	$request
	 * @return	mixed
	 */
	public function ShowProductsPage(Request $request)
	{
		$this->LogFunction("ShowProductsPage()");

		$store = app('store');
		$stores = Store::all();
		$categories = Category::all();
		$product_types = ProductType::all();
		$store_id = $store->id;
		$category_id = 0;
		$this->LogMsg("Default store ID [".$store->id."]");
		$query = $request->input();
		foreach($query as $n=>$v)
		{
			if(is_string($n)== true)
			{
				if(is_string($v)== true)
				{
					$this->LogMsg("Checking query N= $n while V= $v");
					if($n=="s") $store_id = $v;
					if($n=="c") $category_id = $v;
				}
			}
		}
		$this->LogMsg("Required store ID [".$store_id."] Category ID [".$category_id."]");

		$products = array();
		#
		# Get all products in the matching category
		#
		if(($store_id > 0)&&($category_id>0))
		{
			$this->LogMsg("Processing for Store/Category [".$store_id."/".$category_id."]");
			$products_in_category = CategoryProduct::where('category_id', $category_id )->get();
			foreach($products_in_category as $pic)
			{
				array_push($products, Product::find($pic->product_id));
			}
		}
		elseif($store_id > 0)
		{
			$this->LogMsg("Processing for Store Only [".$store_id."/".$category_id."]");
			#
			# {FIX_2018-02-25} Disabled code that gets all products for all categories for store.
			# get all products in all categories for the selected store
			#
			# {FIX_2018-02-25} Converted to first category to reduce number of products show
			#
			$first_category = Category::where('category_store_id',$store_id)
				->where('category_status',"A")
				->first();
			if(is_null($first_category)==false)
			{
				$products_in_category = CategoryProduct::where('category_id', $first_category->id )->get();
				foreach($products_in_category as $pic)
				{
					array_push($products, Product::find($pic->product_id) );
				}
			}
		}
		else
		{
			$this->LogMsg("Processing for All Products [".$store_id."/".$category_id."]");
			#
			# Just get ALL products, need to redo this later
			#
			$products = Product::where('prod_status',"A")->get();
		}
		return view('Admin.Products.products',[
			'store_id'=>$store_id,
			'category_id'=>$category_id,
			'store'=>$store,
			'stores'=>$stores,
			'categories'=>$categories,
			'products'=>$products,
			'product_types'=>$product_types
			]);
	}




	/**
	 * Call the view to present the "Add New" Product page
	 * was "/admin/product/addnew"
	 *
	 * GET ROUTE: /admin/select/type
	 *
	 * @return mixed - view object
	 */
	public function SelectType()
	{
		$this->LogFunction("SelectType()");

		$store = app('store');
		$stores = Store::get();
		$product_types = ProductType::get();

		return view('Admin.Products.selecttype',[
			'store'=>$store,
			'stores'=>$stores,
			'product_types'=>$product_types
			]);
	}


	/**
	 *
	 *
	 * POST ROUTE: /admin/select/type/{id}
	 *
	 * @return mixed 
	 */
	public function RouteToPage($id)
	{
		$this->LogFunction("RouteToPage()");

		$product_type = ProductType::find($id);
		$product_types = ProductType::get();

		$store = app('store');
		$stores = Store::get();
		$categories = Category::get();
		$attributes = Attribute::where('store_id',$store->id)->get();

		$ProductType = \App\Services\Products\ProductTypeFactory::BuildRoute($product_type->id);
		$route = "Admin.Products.".$ProductType;
		$this->LogMsg("Routing to [".$ProductType."]");
		return view($route,[
			'product_types'=>$product_types,
			'product_type'=>$product_type,
			'attributes'=>$attributes,
			'stores'=>$stores,
			'store'=>$store,
			'categories'=>$categories]);
	}




	/**
	 * Present a new page which allows SKU entry, then post back.
	 *
	 * {FIX_2017-10-24} Refactored product fetch using eloquent call in ShowCopyProductPage()
	 *
	 * GET ROUTE: /admin/product/copy/{id}
	 *
	 * @param	integer	$id		Product to copy
	 * @return	mixed
	 */
	public function ShowCopyProductPage($id)
	{
		$this->LogFunction("ShowCopyProductPage()");
		$product = Product::find($id);
		return view('Admin.Products.copy',['product'=>$product]);
	}



	/**
	 * Using the new SKU posted back in the form:
	 * - Read the existing product using the ID,
	 * - Insert a new product with the new SKU.
	 * - Dont copy the images.
	 * - Dont match the categories.
	 *
	 * POST ROUTE: /admin/product/copy/{id}
	 *
	 * @param	integer	$id		Product to use as a template to copy from.
	 * @return	mixed
	 */
	public function CopyProductPage(Request $request, $id)
	{
		$this->LogFunction("CopyProductPage()");
		$this->LogMsg("Source Product ID [".$id."]");

		$base_product = Product::find($id);
		$duplicate_count  = Product::where('prod_sku',$request['prod_sku'])->count();
		if($duplicate_count == 0)
		{
			$base_product['prod_sku'] = $request['prod_sku'];
			$prod_categories = CategoryProduct::where('product_id',$id)->get();
			foreach($prod_categories as $pc)
			{
				$this->LogMsg("Product is assigned to category [".$pc->category_id."]");
			}

			$data = $base_product->toArray();
			$this->LogMsg("New Product".print_r($data, true));
			$new_pid = ProductService::insertArray($data);
			$this->LogMsg("Product [".$id."] copied, new Product ID [".$new_pid."]");
			$saved_categories = array();
			$this->LogMsg("Checking for duplicates?");
			foreach($prod_categories as $pc)
			{
				if(!in_array($pc->category_id, $saved_categories))
				{
					$this->LogMsg("Insert Cat [".$pc->category_id."]   Prod [".$new_pid."]");
					$o = new CategoryProduct;
					$o->category_id = $pc->category_id;
					$o->product_id  = $new_pid;
					$o->save();
					array_push($saved_categories, $pc->category_id);
				}
				else
				{
					$this->LogMsg("Duplicate category found [".$pc->category_id."]");
				}
			}
		}
		else
		{
			\Session::flash('flash_error','ERROR - Product SKU alreay in Database!');
		}
		return Redirect::to("/admin/products");
	}



	/**
	 * Render a view edit page, first collect the existing data and
	 * format it up for the view we are about to call.
	 *
	 * GET ROUTE: /admin/product/edit/{id}
	 *
	 * @param	integer		$id
	 * @return	mixed
	 */
	public function ShowEditProductPage($id)
	{
		$this->LogFunction("ShowEditProductPage(".$id.")");

		$product = Product::find($id);
		if(!is_null($product))
		{
			$this->LogMsg("Found Product (sku) [".$product->prod_sku."]");
			$store = app('store');
			$stores = Store::all();
			$attributes = Attribute::where('store_id',$store->id)->get();
			$product_attributes = AttributeProduct::where('product_id',$product->id)->orderby('combine_order')->get();
			$category_collection = Category::all();
			$categories = array();
			foreach($category_collection as $cc)
			{
				if(is_null($cc->category_store_id))
				{
					$cc->category_store_id = 0;
				}
				array_push($categories, $cc);
			}
			$product_types = ProductType::all();
			$page_object = ProductPageFactory::build($product);
			$view = "edit_".$page_object->getAdminPageRoute();
			$imagemap = ProdImageMap::where('product_id',$id)->get();
			$prod_categories = CategoryProduct::where('product_id',$id)->get();
			$images = array();
			foreach($imagemap as $mapping)
			{
				$this->LogMsg("Found image ID [".$mapping->image_id."]");
				$img = Image::find($mapping->image_id);
				$this->LogMsg("           Name[".$img->image_file_name."]");
				array_push($images, $img);
			}
			$text = "There are ".sizeof($images)." images assembled.";
			$this->LogMsg( $text );

			return view('Admin.Products.'.$view, [
				'product'=>$product,
				'product_types'=>$product_types,
				'product_categories'=>$prod_categories,
				'attributes'=>$attributes,
				'product_attributes'=>$product_attributes,
				'images'=>$images,
				'categories'=>$categories,
				'store'=>$store,
				'stores'=>$stores,
				'catmappings'=>$prod_categories]);
		}
		\Session::flash('flash_error','ERROR - Selected Product is invalid!');
		return Redirect::to("/admin/products");
	}

}
