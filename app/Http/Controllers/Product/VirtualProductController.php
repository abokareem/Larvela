<?php
/**
 * \class	VirtualProductController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-04-03
 * \version	1.0.9
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
namespace App\Http\Controllers\Product;


use Input;
use Redirect;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;


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
 * {INFO_2018-07-19} Added Support for BackInStockEMail
 * {INFO_2018-10-16} Added Support for Multiple Product file uploads
 */
class VirtualProductController extends Controller
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
	 2018-10-16|16:11:46|OK|VirtualProductController|Illuminate\Http\UploadedFile Object
	 (
	     [test:Symfony\Component\HttpFoundation\File\UploadedFile:private] => 
     [originalName:Symfony\Component\HttpFoundation\File\UploadedFile:private] => 905-Santorini-17810367345809.jpg
	     [mimeType:Symfony\Component\HttpFoundation\File\UploadedFile:private] => image/jpeg
	     [size:Symfony\Component\HttpFoundation\File\UploadedFile:private] => 128687
	     [error:Symfony\Component\HttpFoundation\File\UploadedFile:private] => 0
	     [hashName:protected] => 
	     [pathName:SplFileInfo:private] => /tmp/phpgE38IH
	     [fileName:SplFileInfo:private] => phpgE38IH
	 )
	 *
	 * @param	integer	$id	product ID
	 * @return	integer
	 */
	public function SaveUploadedFile(ProductRequest $request, $id)
	{
		$this->LogFunction("SaveUploadedFile(".$id.")");
		
		$bad_extensions = array("exe","bat","com","cmd","bin","cpl","csh","sh","inf","ins","inx","lnk","out","ps1","scr","sct","u3p","run","reg","rgs","vbs","shb","vb","ws","wsf","wsh");
		$file_count = 0;
		$file_name = "N/A";
		if($request->hasFile('dfile'))
		{
			$this->LogMsg("There are files in request");
			$files = $request->file('dfile');
			$file_count = sizeof($files);
			foreach($files as $file)
			{
				$this->LogMsg( print_r($file,1) );
				$file_type = explode("/",$file->getClientMimeType());
				$file_extension = strtolower($file_type[1]);
				$file_path = $this->getDownloadPath($id);
				$file_name = $file->getClientOriginalName();
				$this->LogMsg("File name [".$file_name."]");
				if(!in_array($file_extension, $bad_extensions))
				{
					$file->move($file_path,$file_name);
					$this->LogMsg(" - Moved");
				}
				$this->LogMsg("     Type [".print_r($file_type,1)."]");
				$this->LogMsg("      Ext [".$file_extension."]");
				$this->LogMsg("     Path [".$file_path."]");
			}
		}
		else
		{
			$this->LogError("Invalid file Data.");
			\Session::flash('flash_error',"ERROR - Invalid file type");
		}
		return $file_count;
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

		$store = app('store');
		CategoryProduct::where('product_id',$id)->delete();
		$product = Product::find($id);
		$request->UpdateProduct($id);
		$this->SaveImages($request,$id);
		#
		# get the product and if the qty was 0 and is now >0 then call Back In Stock
		#
		$this->LogMsg("Check stock levels for Product [".$id."] - [".$product->prod_sku."]");
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
		$this->SaveUploadedFile($request, $id);
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
		$pid = $request->SaveProduct();
		$this->LogMsg("Insert New Product new ID [".$pid."]");
		$product = Product::find($pid);
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
