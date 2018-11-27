<?php
/**
 * \class	StoreFrontController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-18
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

use App\Http\Requests;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;

use Auth;
use Input;
use Cookie;
use Config;
use Session;

use App\User;
use App\Models\Cart;
use App\Models\Image;
use App\Models\Store;
use App\Models\Advert;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Attribute;
use App\Models\CustSource;
use App\Models\ProductType;
use App\Models\StoreSetting;
use App\Models\ProdImageMap;
use App\Models\AttributeValue;
use App\Models\CategoryProduct;
use App\Models\AttributeProduct;
use App\Jobs\ConfirmSubscription;

use App\Services\ProductFilters\FilterProducts;

use App\Traits\Logger;
use App\Traits\PathManagementTrait;
use App\Traits\ProductImageHandling;

/**
 * \brief Manages generating the Storefront and Category page views and some minor related tasks.
 *
 * In a system with no Administration sub-system, this Controller is required. 
 *
 * {FIX_2017-01-10} "StoreFrontController.php" - Added CID value to cookie to trap the new customer ID in the browser as an encrypted cookie.
 * {FIX_2017-07-09} "StoreFrontController.php" - Outof stock notify call failing with 500's.
 * {INFO_2017-08-29} "StoreFrontController.php" - Added support for Themes :)
 * {INFO_2017-09-23} "StoreFrontController.php" - Starting to add virtual product support and attributes.
 * {INFO_2017-10-28} "StoreFrontController.php" - Refactored to use new classes.
 * {INFO_2018-01-13} "StoreFrontController.php" - Added support for attributes such as size on front page.
 * {INFO_2018-09-12} "StoreFrontController.php" - Removed ShowProductPage into its own controller.
 */
class StoreFrontController extends Controller
{
use Logger;
use PathManagementTrait;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("StoreFrontController");
		$this->LogStart();
		if($cid=Cookie::get('cid','0') > 0)
		{
			Session::set('cid', $cid);
		}
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
	 * Render the view for the Main Store Front "Home" page.
	 * Comprised of a range of random products.
	 * Plenty of scope to use different products/categories etc.
	 *
	 * @param	integer	$MAXPRODUCTS	Count of products to show on the home page
	 * @return	mixed	View object with data
	 */
	public function ShowStoreFront($MAXPRODUCTS = 9)
	{
		$this->LogFunction("ShowStoreFront()");
		#
		# Check if the first user (admin) is present,
		# if not run the install, need app key in install to proceed.
		#
		if(Schema::hasTable('users'))
		{
			$this->LogMsg("User table is present");
			if(User::count()==0)
			{
				return view('Install.install-1');
			}
		}

		$store = app('store');

		$filter = new FilterProducts;
		$products = $filter->ReturnProducts();
		$categories = Category::where('category_store_id',$store->id)->get();
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$attributes = Attribute::where('store_id',$store->id)->get()->toArray();
		$attribute_values = AttributeValue::get();
		if(sizeof($products)==0)
		{
			$sizes = array();
			$colours = array();
			$size_attribute = Attribute::where('attribute_name','Size')->where('store_id',$store->id)->first();
			if(!is_null($size_attribute))
			{
				$sizes = AttributeValue::where('attr_id',$size_attribute->id)->get();
			}
			$colour_attribute = Attribute::where('attribute_name','Colour')->where('store_id',$store->id)->first();
			if(!is_null($colour_attribute))
			{
				$colours = AttributeValue::where('attr_id',$colour_attribute->id)->get();
			}
			$this->LogMsg("Render View Frontend.storefront - No Products assigned to shop");
			$theme_path = \Config::get('THEME_HOME')."storefront";
			return view($theme_path,[
				'store'=>$store,
				'adverts'=>array(),
				'categories'=>$categories,
				'settings'=>$settings,
				'products'=>array(),
				'attributes'=>$attributes,
				'attribute_values'=>$attribute_values,
				'sizes'=>$sizes,
				'colours'=>$colours
				]);
		}
		$display_products = array();
		foreach($products as $product)
		{
			$image = '/media/product-image-missing.jpeg';
			$image_path = $this->getStoragePath($product->id);
			$found=0;

			$displayable_product = new \stdClass;
			$displayable_product->id = $product->id;
			$displayable_product->prod_title = $product->prod_title;
			$displayable_product->prod_type = $product->prod_type;
			$displayable_product->prod_qty = $product->prod_qty;
			$displayable_product->prod_short_desc = $product->prod_short_desc;
			$displayable_product->prod_retail_cost = $product->prod_retail_cost;
			$displayable_product->product = $product;
			$displayable_product->image = '/media/product-image-missing.jpeg';

			$product_images = ProdImageMap::where('product_id',$product->id)->get();
			foreach($product_images as $img)
			{
				$this->LogMsg("Found $img->id  IMG $img->image_id  -- PROD $img->product_id ");
				$image_data = Image::find($img->image_id);
				$thumbs = Image::where('image_parent_id',$img->image_id)->get();
				foreach($thumbs as $ti)
				{
					if($ti->image_width == 310 )
					{
						$found++;
						$imagefile = "/".$ti->image_folder_name."/".$ti->image_file_name;
						$displayable_product->image = $imagefile;
						break;
					}
				}
				if($found) break;
			}
			$cat_ids = CategoryProduct::where('product_id',$product->id)->get();
			if(sizeof($cat_ids)>0) 
			{
				$displayable_product->category = Category::find($cat_ids[0]->category_id)->category_title;
			}
			else
			{
				$displayable_product->category = "unknown";
			}
			array_push($display_products, $displayable_product);
		}
		$theme_path = \Config::get('THEME_HOME')."storefront";
		$this->LogMsg("Render View storefront from [".$theme_path."]");

		return view($theme_path,[
			'store'=>$store,
			'adverts'=>array(),
			'categories'=>$categories,
			'settings'=>$settings,
			'products'=>$display_products,
			'attributes'=>$attributes,
			'attribute_values'=>$attribute_values,
			]);
	}	



	/**
	 * If a menu is used to narrow products by size/colour etc
	 * then we are using the attributes table and attribute_products table
	 * to select products that have the required attribute (all "Small" sizes for instance).
	 *
	 * Use attributes and parent products to return a collection of basic products.
	 *
	 *
	 * @param	integer	$id	The product_attribute ID to find
	 * @return	mixed
	 */
	public function ShopByAttribute($id)
	{
		$this->LogFunction("ShopByAttribute( $id )");

		$attribute_value = AttributeValue::find($id);
		$parent_product = ProductType::where('product_type','Parent Product')->first();
		
		$this->LogMsg("Parent Product Found ID [".$parent_product->id."] = [".$parent_product->product_type."]");
		#
		# Most common attributes are Size and Colour
		#
		$size_attribute = Attribute::where('attribute_name','Size')->first();
		$this->LogMsg("Size Attribute Found ID [".$size_attribute->id."] = [".$size_attribute->attribute_name."]");

		$sizes = AttributeValue::where('attr_id',$size_attribute->id)->get();
		foreach($sizes as $s)
		{
			$this->LogMsg("Size ID [".$s->id."]");
			$this->LogMsg("        [".$s->attr_id."]");
			$this->LogMsg("        [".$s->attr_value."]");
		}
		$colour_attribute = Attribute::where('attribute_name','Colour')->first();
		$this->LogMsg("Colour Attribute Found ID [".$colour_attribute->id."] = [".$colour_attribute->attribute_name."]");
		$colours = AttributeValue::where('attr_id',$colour_attribute->id)->get();

		#
		# build SKU from attributes to get applicable products
		# (alternatively use a tabe to map products to attributes).
		#
		$parent_products = Product::where('prod_type',$parent_product->id)->get();
		$product_rows = array();
		foreach($parent_products as $parent)
		{
			$sku = $parent->prod_sku."-%".$attribute_value->attr_value."%";
			$matches = Product::where('prod_sku','like',$sku)->get();
			foreach($matches as $item)
				array_push($product_rows, $item);
		}

		$current_store = app('store');
		$settings = StoreSetting::all();

		#
		# Now have all relevant product from search - get images and build data for view
		#
		if(sizeof($product_rows) > 0)
		{
			$categories = Category::where('category_store_id',0)->get();
			/*
			 * Select products in random order using the product_id_list as our range.
			 * Scan through product_rows to find item by ID as its NOT in order.
			 */
	
			/*
			 * Use "selected" to keep check of a product we have already picked out of the list
			 */
			$selected = array();

			/*
			 * Return "products" to view.
			 */
			$products = array();
			$this->LogMsg("Buidling product data");
			foreach($product_rows as $product)
			{
				$pid = $product->id;
				$this->LogMsg("Product ID selected [ $pid ]");
				$image = '/media/product-image-missing.jpeg';
				$imagefile = $image;
				$path = $this->getStoragePath($pid);
				$found = 0;
				/*
				 * Find all images mapped to the product and select the one we can use on the store front
				 * This image was generated by our ResizeImage Job when the image was uplaoded.
				 */
				$product_images = $product->images()->get();

				foreach($product_images as $img)
				{
					$this->LogMsg("Found $img->id  IMG $img->image_id  -- PROD $img->product_id ");
					$image_data = Image::find($img->image_id);
					$thumbs = Image::where('image_parent_id', $img->id)->get();
					foreach($thumbs as $ti)
					{
						if($ti->image_width == 310 )
						{
							$found++;
							$imagefile = "/".$ti->image_folder_name."/".$ti->image_file_name;
							$product->image = $imagefile;
							break;
						}
					}
					if($found) break;
				}
				if($found==0)
				{
					$product->image = $imagefile;
				}
	
				$cat_ids = CategoryProduct::where('product_id',$pid)->get();
				if(sizeof($cat_ids)>0) 
				{
					$product->category = Category::find($cat_ids[0]->category_id)->category_title;
				}
				else
					$product->category = "unknown";
				array_push($selected, $pid);
				array_push($products, $product);
			}

			$theme_path = \Config::get('THEME_HOME')."storefront";
			$this->LogMsg("Render View storefront from [".$theme_path."]");
			return view($theme_path,[
				'store'=>$current_store,
				'current_store'=>$current_store,
				'settings'=>$settings,
				'sizes'=>$sizes,
				'colours'=>$colours,
				'categories'=>$categories,
				'adverts'=>$this->getAdverts(),
				'products'=>$products ]);
		}
		else
		{
			$this->LogMsg("Render View --- no attributes found");
			$theme_path = \Config::get('THEME_ERRORS')."no-matching-products";
			return view($theme_path,[
				'store'=>$current_store,
				'current_store'=>$current_store,
				'settings'=>$settings ]);
		}
	}





	/**
	 * AJAX REQUEST -  Count number of items in cart and totalize the cost, return it as JSON data.
	 *
	 * Returns "C" for count of items in cart and "V" for the value of the items (Formatted)
	 *
	 * @return string JSON data with status code
	 */
	public function GetCartData()
	{
		$this->LogFunction("GetCartData()");

		$c = 0;
		$v = 0;
		if(Request::ajax())
		{
			$this->LogMsg("AJAX request OK");
			if(Auth::check())
			{
				$this->LogMsg("Auth check OK");
				$cart = Cart::where('user_id',Auth::user()->id)->first();
				if($cart)
				{
					$this->LogMsg("cart oK");
					$items = $cart->items;
					if(!is_null($items))
					{
						$this->LogMsg("cart has [".sizeof($items)."] items");
						foreach($items as $item)
						{
							$product = Product::find($item->product_id);
							$v += $product->prod_retail_cost;
							$c += $item->qty;
						}
						$data = array('c'=>$c,'v'=>number_format($v,2));
						return json_encode($data);
					}
				}
			}
		}
		$data = array('c'=>0,'v'=>0);
		return json_encode($data);
	}
}
