<?php
/**
 * \class	BasicProductController
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
 * \addtogroup  Product_Types
 * BasicProductController - Provides CRUD like functions for "BASIC" type products.
 */
namespace App\Http\Controllers\Product;


use Input;
use Redirect;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ProductRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Bus\Dispatcher;

use App\Helpers\StoreHelper;
use App\Services\ProductService;
use App\Services\CategoryService;

use App\Jobs\BackInStock;
use App\Jobs\ResizeImages;
use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Mail\BackInStockEmail;

use App\Models\Store;
use App\Models\Image;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductType;
use App\Models\ProdImageMap;
use App\Models\Notification;
use App\Models\CategoryProduct;


use App\Traits\Logger;
use App\Traits\PathManagementTrait;
use App\Traits\ProductImageHandling;


/**
 * \brief MVC Controller to Handle the Product Administration functions.
 *
 * {INFO_2017-09-11} Added support for prod_has_free_shipping
 * {INFO_2017-10-26} Added Support for BackInStock Job dispatch
 * {INFO_2018-07-26} Added Support for BackInStockEmail dispatch
 */
class BasicProductController extends Controller
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
		$this->setFileName("larvela-admin");
		$this->setClassName("BasicProductController");
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
	 * Given the ID of an image remove it totally from the server.
	 * Return back to the product edit page we were on when we pressed delete.
	 *
	 * @param	integer	$id		Image to delete
	 * @param	integer	$pid	Product ID to return to
	 * @return	mixed
	 */
	public function DeleteImage($id,$pid)
	{
		$this->LogFunction("DeleteImage(".$id.",".$pid.")");

		$text = "Image ID [".$id."]";
		$this->LogMsg($text);
		$text = "Product ID [".$pid."]";
		$this->LogMsg($text);

		dispatch(new DeleteImageJob($id));
		return Redirect::to("/admin/products");
	}



	/**
	 * Present product images and show an upload form
	 * $id is product id to use.
	 *
	 * GET ROUTE: /admin/prodimage/edit/{id}
	 *
	 * {FIX_2017-10-24} Refactored product fetch using eloquent call in ShowImageUploadPage()
	 *
	 * @param $id int - row ID from products table
	 * @return mixed - view object
	 */
	public function ShowImageUploadPage($id)
	{
		$this->LogFunction("ShowImageUploadPage(".$id.")");

		$product = Product::find($id);
		$images = ProdImageMap::where('product_id',$id)->get();
		return view('Admin.Products.editproductimages',[ 'product'=>$product, 'images'=>$images ]);
	}



	/**
	 * POST ROUTE: /admin/prodimage/update/{id}
	 *
	 * ToDo
	 * @pre none
	 * @post none
	 * @param $id int row id of product
	 * @return void
	 */
	public function SaveProdImages($id)
	{
		#
		# @tod Add code to save the image etc
		#
		$this->LogFunction("SaveProdImages(".$id.")");
	}






	/**
	 * Update the products table with our changes (if any).
	 *
	 * POST ROUTE: /admin/product/update/{id}
	 *
	 * @pre		Form must present all valid columns
	 * @post	New rows inserted into database table "category_products" 
	 * @param	mixed	$request - Validation request object
	 * @param	integer	$id - Row id to be checked against before insert
	 * @return	mixed
	 */
	public function Update(Request $request, $id)
	{
		$this->LogFunction("UpdateProduct(".$id.")");
		
		$this->SaveImages($request,$id);
		$product = Product::find($id);
		CategoryProduct::where('product_id',$id)->delete();
		CategoryService::AssignCategories($request,$id);
		#
		# get the product and if the qty was 0 and is now >0 then call Back In Stock
		#
		$this->LogMsg("Check stock levels for Product [".$id."] - [".$product->prod_sku."]");
		$store = app('store');
		if($product->prod_qty == 0)
		{
			$this->LogMsg("Existing stock level is 0");
			if($request['prod_qty'] > 0)
			{
				$this->LogMsg("Stock Level increased to [".$request['prod_qty']."]");
				#
				# This may need to be spun off into a separate task with a flag set somewhere with the product ID
				# thats been updated.
				#
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
		return Redirect::to("/admin/products");
	}




	/**
	 * Save the form just posted back to the server
	 * Handle the images (if any) separately.
	 *
	 * POST ROUTE: /admin/product/save
	 *
	 * @pre form must present all valid columns
	 * @post new row inserted into database table "products" 
	 * @param	ProductRequest	$request mixed Validation request object
	 * @return	mixed 
	 */
	public function Save(ProductRequest $request)
	{
		$this->LogFunction("SaveNewProduct()");

		$store=app('store');
		$pid = $request->SaveProduct();
		$this->LogMsg("Insert New Product new ID [".$pid."]");
		$this->SaveImages($request,$pid);
        $store_id = $store->id;
        $category_id = 0;
		$this->LogMsg("Default store ID [".$store->id."]");
		#
		# @todo Replace with QueryFilter class
		#
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
		$this->LogMsg("Required store ID [".$store_id."]");
		$this->LogMsg("Required Category ID [".$category_id."]");
		return Redirect::to("/admin/products?c=".$category_id."&s=".$store_id);
	}




	/**
	 * Given the ID of a product remove it totally from the server.
	 * use a form because only admin can get the form and the ID
	 * must be encoded in the form and the call must be authenticated as an admin user.
	 *
	 * CALLED FROM FACTORY
	 *
	 * @param	integer	$id		Product to delete
	 * @return	mixed
	 */
	public function Delete(Request $request, $id )
	{
		$this->LogFunction("DeleteProduct(".$id.")");

		$store = app('store');
		$form = Input::all();
		if(array_key_exists('id',$form))
		{
			if($id == $form['id'])
			{
				$this->LogMsg("Dispatch Job.");
				$this->dispatch(new DeleteProductJob($id));
			}
			else
			{
				$this->LogError("Mismatched product id.");
				\Session::flash('flash_error',"ERROR - Product ID is invalid!");
			}
		}
		else
		{
			$this->LogError("Invalid product id.");
			\Session::flash('flash_error',"ERROR - Product ID is invalid!");
		}
		return Redirect::to("/admin/products");
	}



	/**
	 * return boolean true if this Product type
	 * has children products.
	 *
	 * @return	boolean
	 */
	public function hasChildren()
	{
		return false;
	}
}
