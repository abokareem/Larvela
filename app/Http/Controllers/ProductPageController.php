<?php
/**
 * \class	ProductPageController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-09-12
 * \version	1.0.0
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

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Route;
use App\Http\Requests;

use Auth;
use Input;
use Cookie;
use Session;
use Config;

use App\User;

use App\Models\Attribute;
use App\Models\AttributeProduct;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProdImageMap;
use App\Models\Image;
use App\Models\Store;
use App\Models\StoreSetting;


use App\Traits\Logger;


/**
 * \brief Manages generating the Product Page view, allows for differnet product types such as basic, parent and virtual
 * In a system with no Administration sub-system, this Controller is required. 
 *
 * {INFO_2018-09-12} "ProductPageController.php" - Created from StoreFrontController code.
 */
class ProductPageController extends Controller
{
use Logger;



	/**
	 * Setup logging
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("store");
		$this->setClassName("ProductPageController");
		$this->LogStart();
	}



	/**
	 * Close off log
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}




	/**
	 * The product page is for an individual product thats been selected.
	 *
	 * Must distinquish between Basic and Parent products.
	 * First assemble all the data collections then return a view.
	 *
	 * Notes:
	 * - Related Products not yet implemented, 
	 * - virtual products not yet implemented.
	 * - Attributes not yet fully implemented.
	 *
	 * @param	integer	$id	row id of product to display
	 * @return	mixed
	 */
	public function ShowProductPage($id)
	{
		$this->LogFunction("ShowProductPage()");
		if(is_numeric($id))
		{
			$Product = new Product;
			$AttributeProduct = new AttributeProduct;

			$store = app('store');
			$settings = StoreSetting::all();

			$related_products = array();
			$images = array();
			$thumbnails = array();
			if($id > 0)
			{
				$product = Product::where('id',$id)->first();
				$image_list = ProdImageMap::where('product_id',$product->id)->get();
				$this->LogMsg("Fetch images for this product");
				foreach($image_list as $i)
				{
					$this->LogMsg("Image [".$i->image_id."]");
					$row = Image::find($i->image_id);
					array_push($images,$row);
					array_push($thumbnails,$row);
				}
				#
				# TODO - need to call a method in Image Model to find the main image given the ID
				#        then read the folder from the DB
				#
				$main_image_folder_name = $this->getStoragePath($id);
				$main_image_file_name = $id."-1.jpeg";
				if(sizeof($images)==1)
				{
					$main_image_file_name = $images[0]->image_file_name;
				}
				$this->LogMsg("Fetching Attributes for PID [".$product->id."]");
				$attributes = Attribute::where('store_id',$store->id)->get()->toArray();
				$product_attributes = AttributeProduct::where('product_id',$product->id)->get();
				switch($product->prod_type)
				{
					case 5:
						$view = 'packproduct';
						break;
					case 4:
						$view = 'vitualproduct';
						break;
					case 3:
						$view = 'limitedvitualproduct';
						break;
					case 2:
						$view = 'parentproduct';
						break;
					case 1:
					default:
						$view = 'productpage';
						break;
				}
				$categories = Category::where('category_store_id',0)->get();

				$theme_path = \Config::get('THEME_PRODUCT').$view;
				return view( $theme_path,[
					'store'=>$store,
					'categories'=>$categories,
					'product'=>$product,
					'attributes'=>$attributes,
					'prod_attributes'=>$product_attributes,
					'images'=>$images,
					'thumbnails'=>$thumbnails,
					'main_image_folder_name'=>$main_image_folder_name,
					'main_image_file_name'=>$main_image_file_name,
					'settings'=>$settings,
					'related'=>$related_products
					]);
			}
			else
			{
			}

		}
	}



	/**
	 * Construct a media image storage path from the ID given and return string
	 *
	 * @param   string  $str_id String holding the ID value as a number
	 * @return  string
	 */
	protected function getStoragePath($str_id)
	{
		$this->LogFunction("getStoragePath()");
		$id = "$str_id";
		$path="/media";
		for($i=0;$i<strlen($id);$i++)
		{
			$path.="/".$id[$i];
			$this->LogMsg($path);
		}
		$this->LogMsg("Path is [ $path ] ");
		return $path;
	}
}
