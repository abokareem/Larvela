<?php
/**
 * \class	ProductPageController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-09-12
 * \version	1.0.7
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

use App\Services\ProductPageFactory;
use App\Services\ImageService;

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
		$this->setFileName("larvela");
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

		#
		# @todo Build related Product system later
		#
		$related_products = array();

		if(is_numeric($id))
		{
			if($id > 0)
			{
				$product = Product::find($id);
				$page_object = ProductPageFactory::build($product);
				$view = $page_object->getPageRoute();
				$variables = $page_object->getPageVariables();

				$main_image_folder_name = $this->getStoragePath($id);
				$main_image_file_name = $id."-1.jpeg";
				$images = $variables['images'];
				if(sizeof($images)==1)
				{
					$main_image_file_name = $images[0]->image_file_name;
				}
				$variables = array_merge(
					$variables,
					['main_image_folder_name'=>$main_image_folder_name]);
				$variables = array_merge(
					$variables,
					['main_image_file_name'=>$main_image_file_name]);
				$variables = array_merge($variables, ['related'=>$related_products]);

				$theme_path = \Config::get('THEME_PRODUCT').$view;
				return view( $theme_path, $variables );
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
