<?php
/**
 * \class	ParentProductController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-18
 * \version	1.0.1
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
 * \addtogroup Store_Administration
 * ParentProductController - THis controller is repsonsible for Parent product administration.
 */
namespace App\Http\Controllers;


use Input;
use Redirect;


use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Mail;


use App\Helpers\StoreHelper;
use App\Services\ProductService;


use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\BackInStock;
use App\Mail\BackInStockEmail;
use App\Jobs\ResizeImages;


use App\Models\Store;
use App\Models\Product;
use App\Models\Image;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\CategoryProduct;

use App\Models\Attribute;
use App\Models\ProdImageMap;
use App\Models\Notification;


use App\Traits\Logger;
use App\Traits\ProductImageHandling;


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
use ProductImageHandling;

	/**
	 * Open log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("store-admin");
		$this->LogStart();
		$this->LogMsg("CLASS:ParentProductController");
	}
	
	/**
	 * Close log file
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS:ParentProductController");
		$this->LogEnd();
	}



	/**
	 * Save the Parent Product
	 * Handle the images (if any) separately.
	 *
	 * POST ROUTE: /admin/product/save-pp
	 *
	 * @pre form must present all valid columns
	 * @post new row inserted into database table "products" 
	 * @param $request mixed Validation request object
	 * @return mixed - view object
	 */
	public function Save(ProductRequest $request)
	{
		$this->LogFunction("Save()");

		$pid=0;
		$categories = $request->categories;	/* array of category id's */
		$pid = ProductService::insert($request);
		$this->LogMsg("Insert New Product new ID [".$pid."]");
		$this->LogMsg("Process product assigned categories");
		if(isset($categories))
		{
			foreach($categories as $c)
			{
				$o = new CategoryProduct;
				$o->product_id = $pid;
				$o->category_id = $c;
				$o->save();
				$this->LogMsg("Insert product ID [".$pid."] with Category ID [".$c."]");
			}
		}
		else
		{
			#
			# @todo Need to assign to somethign or else product will not show up in list!
			#
			$this->LogMsg("No categories to assign (yet)");
		}

		#
		# Need to add this to a trait maybe???
		#
		$this->SaveUploadedImage($pid);
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

		CategoryProduct::where('product_id',$id)->delete();
		$categories = $request->category;	/* array of category id's */
		if(sizeof($categories)>0)
		{
			$this->LogMsg("Assign categories");
			foreach($categories as $c)
			{
				$text = "Assign category ID [".$c."] with product ID [".$id."]";
				$this->LogMsg( $text );
				$o = new CategoryProduct;
				$o->category_id = $c;
				$o->product_id = $id;
				$o->save();
				$this->LogMsg("Insert ID [".$o->id."]");
			}
		}
		else
		{
			$this->LogMsg("No categories to process!");
		}
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
		ProductService::update($request);

		#
		# Move to ProductImage Trait ???
		#
		$this->SaveUploadedImage($id);
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

		return view('Admin.Products.editproduct',[
			'product'=>$product,
			'product_types'=>$product_types,
			'images'=>$images,
			'categories'=>$categories,
			'store'=>$store,
			'stores'=>$stores,
			'catmappings'=>$prod_categories]);
	}



	/**
	 *------------------------------------------------------------
	 *
	 *                        DEVELOPMENT
	 *
	 *------------------------------------------------------------
	 *
	 * Delete parent product if there are no child products under it.
	 *
	 *
	 * @return	void
	 */
	public function Delete(Request $request, $id)
	{
		$this->LogFunction("DebugParentProducts()");
		$this->LogMsg("Delete Parent Product [".$id."]");

	}




}
