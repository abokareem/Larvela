<?php
/**
 * \class	ParentProductController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-18
 * \version	1.0.8
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
 * \addtogroup Product_Types
 * ParentProductController - Provides CRUD like functions for "PARENT" products.
 */
namespace App\Http\Controllers\Product;


use Input;
use Redirect;


use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;


use App\Helpers\StoreHelper;
use App\Services\ProductService;
use App\Services\CategoryService;


use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\BackInStock;
use App\Mail\BackInStockEmail;


use App\Models\Image;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductType;
use App\Models\ProdImageMap;
use App\Models\Notification;
use App\Models\AttributeValue;
use App\Models\CategoryProduct;
use App\Models\AttributeProduct;


use App\Traits\Logger;
use App\Traits\ProductImageHandling;
use App\Traits\PathManagementTrait;


/**
 * \brief MVC Controller to Handle the parent Product functions.
 *
 * {INFO_2017-09-11} Added support for prod_has_free_shipping
 * {INFO_2017-10-26} Added Support for BackInStock Job dispatch
 * {INFO_2018-07-26} Added Support for BackInStockEmail dispatch
 * {INFO_2018-09-23} Changed method calls to be invoked via a factory
 */
class ParentProductController extends Controller
{
use Logger;
use PathManagementTrait;
use ProductImageHandling;

	/**
	 * Open log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("ParentProductController");
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
	 * Save the Parent Product
	 * Handle the images (if any) separately.
	 *
	 * POST ROUTE: /admin/product/save
	 *
	 * @pre		form must present all valid columns
	 * @post	new row inserted into database table "products" 
	 * @param	$request mixed Validation request object
	 * @return	mixed - view object
	 */
	public function Save(ProductRequest $request)
	{
		$this->LogFunction("Save()");

		$form = Input::all();
		$attributes = $form['attributes'];
		$product_id = $request->SaveProduct();
#		$product_id = ProductService::insert($request);
		$this->LogMsg("Insert New Product new ID [".$product_id."]");
		$this->SaveImages($request, $product_id);
		CategoryService::AssignCategories($request, $product_id);

		if(isset($attributes))
		{
			$order = 1;
			foreach($attributes as $a)
			{
				$o= new AttributeProduct;
				$o->product_id = $product_id;
				$o->attribute_id = $a;
				$o->combine_order = $order++;
				$o->save();
				$this->LogMsg("Insert Product - Attribute - Order P[".$product_id."] A[".$a."] [".$order."]");
			}
		}
		return Redirect::to("/admin/products");
	}




	/**
	 * Update the products table with our changes (if any).
	 *
	 * POST ROUTE: /admin/product/update/{id}
	 *
	 * @pre form must present all valid columns
	 * @post new row inserted into database table "products" 
	 * @param $request mixed Validation request object
	 * @param $id int row id to be checked against before insert
	 * @return mixed - view object
	 */
	public function Update(ProductRequest $request, $id)
	{
		$this->LogFunction("Update(".$id.")");
		$this->SaveImages($request, $id);
		CategoryProduct::where('product_id',$id)->delete();
		CategoryService::AssignCategories($request, $id);
		#
		# get the product and if the qty was 0 and is now >0 then call Back In Stock
		#
		$product = Product::find($id);
		$this->LogMsg("Check stock levels for Product [".$id."] - [".$product->prod_sku."]");
		$store = app('store');
		if($product->prod_qty == 0)
		{
			$this->LogMsg("Existing stock level is 0");
			if($request['prod_qty'] > 0)
			{
				$this->LogMsg("Stock Level increased to [".$request['prod_qty']."]");
				$notify_list = Notification::where('product_code',$product->prod_sku)->get();
				$this->LogMsg("Count of notifications to send is [".sizeof($notify_list)."]");
				foreach($notify_list as $n)
				{
					if(strlen($n->email_address)>3)
					{
						$this->LogMsg("Send notify to [".$n->email_address."]");
						dispatch(new BackInStock($store, $n->email_address, $product));
						Mail::to($n->email_address)->send(new BackInStockEmail($store, $n->email_address, $product));
					}
					$n->delete();
				}
			}
		}
		$request['id'] = $id;
		$request->UpdateProduct($id);	#ProductService::update($request);
		return Redirect::to("/admin/products");
	}



	/**
	 * Render a view edit page, first collect the existing data and
	 * format it up for the view we are about to call.
	 *
	 * GET ROUTE: /admin/product/edit/{id}
	 *
	 * @param $id int row id to be checked against before insert
	 * @return mixed - view object
	 */
	public function Edit($id)
	{
		$this->LogFunction("Edit(".$id.")");

		$product = Product::find($id);
		$store = app('store');
		$stores = Store::all();
		$categories = Category::all();
		$product_types = ProductType::all();
		$attributes = Attribute::where('store_id',$store->id)->get();
		$product_attributes = AttributeProduct::where('product_id',$product->id)->orderby('combine_order')->get();

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
		$this->LogMsg("Image array size [".sizeof($images)."]");

		return view('Admin.Products.edit_parent',[
			'store'=>$store,
			'stores'=>$stores,
			'images'=>$images,
			'product'=>$product,
			'categories'=>$categories,
			'attributes'=>$attributes,
			'catmappings'=>$prod_categories,
			'product_types'=>$product_types,
			'product_attributes'=>$product_attributes
			]);
	}



	/**
	 * Delete parent product if there are no child products under it.
	 *
	 * @param	Request	$request
	 * @param	integer	$id
	 * @return	void
	 */
	public function Delete(Request $request, $id)
	{
		$this->LogFunction("DebugParentProducts()");
		$this->LogMsg("Delete Parent Product [".$id."]");

		$parent = Product::find($id);
		$attribute_values = AttributeValue::get();
		$product_attributes = AttributeProduct::where('product_id',$id)->orderby('combine_order')->get();
		$child_products = array();
		if(sizeof($product_attributes)==1)
		{
			foreach($product_attributes as $pa)
			{
				$this->LogMsg("PID [".$pa->product_id."]  Attr [".$pa->attribute_id."]");
				foreach($attribute_values as $at)
				{
					if($at->attr_id == $pa->attribute_id)
					{
						$sku = $parent->prod_sku."-".$at->attr_value;
						$product = Product::where('prod_sku',$sku)->first();
						if(!is_null($product))
						{
							$this->LogMsg("SKU ".$product->prod_sku." - QTY [".$product->prod_qty."]");
							array_push($child_products, $product);
						}
					}
				}
			}
		}
		elseif(sizeof($product_attributes)==2)
		{
			$first_attributes = AttributeValue::where('attr_id',1)->orderby('attr_sort_index')->get();
			$second_attributes = AttributeValue::where('attr_id',2)->orderby('attr_sort_index')->get();
			foreach($first_attributes as $a1)
			{
				foreach($second_attributes as $a2)
				{
					$qty = 0;
					$sku = $parent->prod_sku.'-'.$a1->attr_value."-".$a2->attr_value;
					$product = Product::where('prod_sku',$sku)->first();
					if(!is_null($product))
					{
						$this->LogMsg("SKU ". $product->prod_sku." - QTY [".$product->prod_qty."]");
						array_push($child_products, $product);
					}
				}
			}
		}
		if(sizeof($child_products)>0) return;
		dispatch(new DeleteProductJob($id));
	}



	/**
	 * return boolean true if this Product type
	 * has children products.
	 *
	 * @return	boolean
	 */
	public function hasChildren()
	{
		return true;
	}
}
