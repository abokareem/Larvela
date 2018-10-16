<?php
/**
 * \class	VirtualProductController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-04-03
 * \version	1.0.6
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
 * \addtogroup Product_Types
 * VirtualProductController - Provides CRUD like functions relevant to all "VIRTUAL" products.
 */
namespace App\Http\Controllers;


use Input;
use Redirect;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Mail;


use App\Services\ProductService;


use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\BackInStock;
use App\Jobs\ResizeImages;

use App\Mail\BackInStockEmail;

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
use App\Traits\PathManagementTrait;


/**
 * \brief MVC Controller to Handle the Product Administration functions.
 *
 * {INFO_2017-09-11} Added support for prod_has_free_shipping
 * {INFO_2017-10-26} Added Support for BackInStock Job dispatch
 * {INFO_2018-07-19} Added Support for BackInStockEMail
 */
class VirtualProductController extends Controller
{
use Logger;
use ProductImageHandling;
use PathManagementTrait;


	/**
	 * Open log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("VirtualProductController");
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
	 * Given the ID of a Virtual Product remove it totally from the server.
	 * use a form because only admin can get the form and the ID
	 * must be encoded in the form and the call must be authenticated 
	 * as an admin user.
	 *
	 * Called from the ProductControler $obj->Delete(Request, integer);
	 *
	 * @param	integer	$id		Product to delete
	 * @return	mixed
	 */
	public function Delete(Request $request, $id )
	{
		$this->LogFunction("Delete()");
		$this->LogMsg("Delete Virtual Product [".$id."]");

		$form = Input::all();
		if(array_key_exists('id',$form))
		{
			if($id == $form['id'])
			{
				$product = Product::find($id);
				$this->LogMsg("Dispatch Job.");
				$cmd = new DeleteProductJob($id);
				$this->dispatch($cmd);
				#
				# Delete Virtual product files and downloadable content.
				#
				$type = ProductType::find($product->prod_type);
				switch($type->product_type_token)
				{
					case "VUNLIMITED":
					#
					# @todo Add code to support removing any files.
					#
						$this->LogMsg("Cleanup files for UNLIMITED product.");
						break;
					case "VLIMITED":
					#
					# @todo Add code to support removing any files.
					#
						$this->LogMsg("Cleanup files for LIMITED product.");
						break;
				}
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
	 * Save the uploaded file.
	 *
	 *
	 * @param	integer	$id	product ID
	 * @return	void
	 */
	public function SaveUploadedFile($id)
	{
		$this->LogFunction("SaveUploadedFile(".$id.")");
		$file_data = Input::file('dfile');
		$file_name = "N/A";
		if($file_data)
		{
			$this->LogMsg("Processing File Data");
			$file_type = explode("/",$file_data->getClientMimeType());
			$file_extension = $file_type[1];
			$file_path = $this->getDownloadPath($id);
			$file_name = $file_data->getClientOriginalName();
			$file_data->move($file_path,$file_name);

			$this->LogMsg("File name [".$file_name."]");
			$this->LogMsg("     Type [".print_r($file_type,1)."]");
			$this->LogMsg("      Ext [".$file_extension."]");
		}
		else
		{
			$this->LogError("Invalid file Data.");
			\Session::flash('flash_error',"ERROR - Invalid file type");
		}
		return $file_name;
	}




	/**
	 * Save the image thats been uploaded for this product
	 *
	 * TODO - more work needed on this.
	 *
	 * @param	integer	$id		Product ID
	 * @return	void
	 */
	public function SaveUploadedImage($id)
	{
		$this->LogFunction("SaveUploadedImage(".$id.")");

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
				$base_images = ProdImageMap::where('product_id',$id)->get();
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

				$this->LogMsg("Create Image Entry");
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
				$o->save();
				$iid = $o->id;
				$this->LogMsg("New Image ID [".$iid."]");
				#
				# Use Eloquent to insert into Pivot table
				#
				$image = Image::find($iid);
				$image->products()->attach($id);

				$this->LogMsg("Dispatch resize job");
				dispatch( new ResizeImages($id, $iid) );
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
	 * Update the products table with our changes (if any).
	 *
	 * Called via the Product Factory $obj->Update($id);
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
		$this->SaveUploadedImage($id);
		return Redirect::to("/admin/products");
	}




	/**
	 * Save the form just posted back to the server
	 * Handle the images (if any) separately.
	 *
	 * Called via the Product Factory $obj->Save(Request);
	 *
	 * @pre form must present all valid columns
	 * @post new row inserted into database table "products" 
	 * @param $request mixed Validation request object
	 * @return mixed - view object
	 */
	public function Save(ProductRequest $request)
	{
		$this->LogFunction("Save()");
		$store = app('store');
		$categories = $request->categories;	/* array of category id's */
		$pid = ProductService::insert($request);
		$this->LogMsg("Insert New Product new ID [".$pid."]");
		$this->LogMsg("Process product assigned categories");
		$product = Product::find($pid);

		$form = Input::all();
/*		$saveactions = $form['saveactions'];
		$afteractions = $form['afteractions'];
		if(isset($saveactions))
		{
			dd($saveactions);
		}
		if(isset($afteractions))
		{
			dd($afteractions);
		}
*/


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
			$this->LogMsg("No categories to assign (yet)");
		}
		$filename = $this->SaveUploadedFile($pid);
		$product->prod_combine_code = $filename;
		$product->save();
		$this->SaveUploadedImage($pid);
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
		return Redirect::to("/admin/products?c=".$category_id."&s=".$store_id);
	}



	/**
	 * Return boolean true if this Product type
	 * has children products.
	 *
	 * @return	boolean
	 */
	public function hasChildren()
	{
		return false;
	}
}
