<?php
/**
 * \class	ImageManagement
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-02-25
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
namespace App\Http\Controllers;


use Input;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Requests;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;


use App\Helpers\StoreHelper;

use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\BackInStock;
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


/**
 * \brief MVC Controller to Manage Image/Product details
 */
class ImageManagement extends Controller
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
		$this->setClassname("ImageManagement");
		$this->LogStart();
	}
	
	/**
	 * Close log file
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS:ImageManagement");
		$this->LogEnd();
	}





	/**
	 * Disaply a menu option of stores and categories to get a list of product to select images for.
	 *
	 * GET ROUTE: /admin/images
	 *
	 * @pre		none
	 * @post	none
	 *
	 * @param	Request	$request
	 * @return	mixed
	 */
	public function Show(Request $request)
	{
		$this->LogFunction("Show()");
		
		$store = app('store');
		$stores = Store::all();
		$categories = Category::all();
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
		$this->LogMsg("Required store ID [".$store_id."]");
		$this->LogMsg("Required Category ID [".$category_id."]");

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
				array_push($products, Product::find($pic->product_id) );
			}
		}
		elseif($store_id > 0)
		{
			$this->LogMsg("Processing for Store Only [".$store_id."/".$category_id."]");
			$first_category = Category::where('category_store_id',$store_id)
				->where('category_status',"A")
				->first();
			$products_in_category = CategoryProduct::where('category_id', $first_category->id )->get();
			foreach($products_in_category as $pic)
			{
				array_push($products, Product::find($pic->product_id) );
			}
		}
		else
		{
			$this->LogMsg("Processing for All Products [".$store_id."/".$category_id."]");
			#
			# Just get ALL products
			#
			$products = Product::where('prod_status',"A")->get();
		}
		return view('Admin.Images.showproducts',[
			'store_id'=>$store_id,
			'category_id'=>$category_id,
			'store'=>$store,
			'stores'=>$stores,
			'categories'=>$categories,
			'products'=>$products
			]);
	}


	public function ShowByProduct(Request $request,$pid)
	{
		$this->LogFunction("ShowByProduct()");
		$this->LogMsg("Selected Product ID [".$pid."]");
		
		$product = Product::find($pid);
		$images = $product->images()->get();

		$store = app('store');
		$stores = Store::all();
		$categories = Category::all();
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
		$this->LogMsg("Required store ID [".$store_id."]");
		$this->LogMsg("Required Category ID [".$category_id."]");
		$this->LogMsg("There are ".sizeof($images)." images assembled.");

		$thumbnails = new Collection();
		foreach($images as $image)
		{
			$image_thumbnails = $image->thumbnails()->get();
			$thumbnails = $thumbnails->merge($image_thumbnails);
		}
		return view('Admin.Images.showimages',[
			'store_id'=>$store_id,
			'category_id'=>$category_id,
			'store'=>$store,
			'stores'=>$stores,
			'categories'=>$categories,
			'product'=>$product,
			'images'=>$images,
			'thumbnails'=>$thumbnails
			]);
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

		$cmd = new DeleteImageJob($id);
		$this->dispatch($cmd);
		return $this->ShowEditProductPage($pid);
	}





	/**
	 * Save the image thats been uploaded for this product
	 *
	 * TODO - more work needed on this.
	 *
	 * @return void
	 */
	public function SaveUploadedImage($id)
	{
		$this->LogFunction("SaveUploadedImage(".$id.")");

		$Image = new Image;

		$file_data = Input::file('file');
		if($file_data)
		{
			$this->LogMsg("Processing File Data");
			$file_type = explode("/",$file_data->getClientMimeType());
			$text = "File Data ".print_r($file_type,true);
			$this->LogMsg($text);
			if($file_type[0]=="image")
			{
				$extension = $file_type[1];
				$filename = $file_data->getClientOriginalName();
				$subpath = $this->getStorageSubPath($id+0);
				$filepath = $this->getStoragePath($id+0);
				#
				# if no images mapped then parent image is "product_id"-"image_order"."extension"
				#        otherwise, access prod_image_maps and determine the order of images, so increment image order.
				#
				####$base_images = ProdImageMap::where('product_id',$id)->get();
				$product = Product::find($id);
				$base_images = $product->images()->get();
				$image_index = 1;
				if(sizeof($base_images)>0)
				{
					#
					# fetch all image db records and parse the names for the indexes already assigned.
					#
					# testing
					$this->LogMsg("Process each image.");
					foreach($base_images as $bi)
					{
						$text = "IDX [".$image_index."]";
						$this->LogMsg($text);
						$text = "DATA: ".print_r($bi,true);
						$this->LogMsg($text);
						$img = Image::where('id',$bi->image_id)->first();
						$file_name = explode(".",$img->image_file_name);
						$f_n_parts = explode("-",$file_name[0]);
						$text = "DATA ".print_r($f_n_parts, true);
						$this->LogMsg( $text );
						if($f_n_parts[1] == $image_index)
						{
							$this->LogMsg("Increment image index!");
							$image_index++;
						}
						if($f_n_parts[1] > $image_index)
						{
							$this->LogMsg("Increment image sequence!");
							$image_index = $f_n_parts[1]+1;
						}
					}
				}

				$id_name = $id."-".$image_index.".".$extension;
				$text = "New ID [".$id_name."]";
				$this->LogMsg($text);

				$file_data->move($filepath,$id_name);
				$newname = $filepath."/".$id_name;
				$text = "New File name [".$newname."]";
				$this->LogMsg($text);

				list($width, $height, $type, $attr) = getimagesize($newname);
				$size = filesize($newname);
				$o = new Image;
				$o->image_file_name = $id_name;
				$o->image_folder_name = $subpath;
				$o->image_size = $size;
				$o->image_height = $height;
				$o->image_width = $width;
				$o->image_parent_id = 0;
				$o->image_order = 0;

				$this->LogMsg("Create Image Entry");
				$o->save();
				$iid = $o->id;
				$text = "New Image ID [".$iid."]";
				$this->LogMsg($text);
				#
				# Use Eloquent to insert into Pivot table
				#
				$image = Image::find($iid);
				$image->products()->attach($id);

				$this->LogMsg("Dispatch resize job");
				dispatch( new ResizeImages($id, $iid) );
				$this->LogMsg("Back in Controller");
			}
			else
			{
				$this->LogError("Invalid file type.");
				\Session::flash('flash_error',"ERROR - Only images are allowed!");
			}
		}
		$this->LogMsg("function Done");
	}




	/**
	 *============================================================
	 *
	 *                NOT IN PRODUCTION USE
	 *
	 *============================================================
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
		$images = $product->images()->get();
		return view('Admin.Products.editproductimages',[ 'product'=>$product, 'images'=>$images ]);
	}




}
