<?php
/**
 * \class	StoreFrontController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-18
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

use App\Models\Advert;
use App\Models\Attribute;
use App\Models\AttributeProduct;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\CustSource;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProdImageMap;
use App\Models\Image;
use App\Models\ImageProduct;
use App\Models\Store;
use App\Models\StoreSetting;
use App\Models\Notification;
use App\Models\SubscriptionRequest;
use App\Models\Users;

use App\Helpers\StoreHelper;

use App\Jobs\OrderPlaced;
use App\Jobs\OrderDispatched;
use App\Jobs\ConfirmSubscription;
use App\Jobs\SubscriptionConfirmed;
use App\Jobs\Welcome;
use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\BackInStock;

use App\Traits\Logger;


/**
 * \brief Manages generating the Storefront and Category page views and some minor related tasks.
 *
 * In a system with no Administration sub-system, this Controller is required. 
 *
 * {FIX_2017-01-10} "StoreFrontController.php" - Added CID value to cookie to trap the new customer ID in the browser as an encrypted cookie.
 * {FIX_2017-07-09} "StoreFrontController.php" - Outof stock notify call failing with 500's
 * {INFO_2017-08-29} "StoreFrontController.php" - Added support for Themes :)
 * {INFO_2017-09-23} "StoreFrontController.php" - Starting to add virtual product support and attributes.
 * {INFO_2017-10-28} "StoreFrontController.php" - Refactored to use new classes
 * {INFO_2018-01-13} "StoreFrontController.php" - Added support for attributes such as size on front page
 */
class StoreFrontController extends Controller
{
use Logger;

/**
 * Store settings from DB
 * @var array $settings
 */
protected $global_settings;
protected $store_settings;


	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$store = app("store");
		#
		# gather settings that we are interested in and apply them.
		#
		$this->global_settings = StoreSetting::where('setting_store_id',0)->get();
		$this->store_settings  = StoreSetting::where('setting_store_id',$store->id)->get();
		foreach($this->global_settings as $entry)
		{
			if($entry->setting_name == "ENABLE_LOGGING")
			{
				if($entry->setting_value == "1")
				{
				# 
				}
			}
		}
		$this->setFileName("store");
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
	 *
	 * @param	integer	$MAXPRODUCTS	Count of products to show on the home page
	 * @return	mixed	View object with data
	 */
	public function ShowStoreFront($MAXPRODUCTS = 9)
	{
		$this->LogFunction("ShowStoreFront()");
		#
		# Check if the first user (admin) is present, if not run the install, need app key in install to proceed.
		#
		if(Schema::hasTable('users'))
		{
			$admin = Users::first();
			if(is_null($admin))
			{
				return view('Install.install-1');
			}
		}
		$Image = new Image;
		$Category = new Category;
		$Product = new Product;
		$CategoryProduct = new CategoryProduct;
		/*
		 * Need to get a sample of products relevant to this store
		 * and limit them so the visitor can drill down by category.
		 */
		$current_store = app('store');
		$settings = StoreSetting::all();
		$this->LogMsg("Current Store: ".$current_store->store_name );
		/*
		 * Categories relevant to this shop
		 */	
		$categories = array();
		/*
		 * DB rows from
		 */	
		$product_rows = array();
		/*
		 * $product_id_list contains all product id's includes duplicates. We need to remove duplicates later and sort list.
		 */	
		$product_id_list = array();
		if($current_store != NULL)
		{
			$this->LogMsg("Load categories for store");
			$categories = Category::where('category_store_id',$current_store->id)->get();
		}
		else
		{
			$this->LogError("Store data not loaded - no categories available");
		}
		/*
		 * Build a list of all products for this store.
		 * NOTE: A product may be in more than 1 category so avoid duplicates.
		 */
		$product_count = 0;
		foreach($categories as $category)
		{
			$this->LogMsg("Loading Products for Category [".$category->id."]");
			$cat_prod_rows = CategoryProduct::where('category_id',$category->id)->get();

			foreach($cat_prod_rows as $cat_prod_row)
			{
				$product = Product::find($cat_prod_row->product_id);
				if($product->prod_visible == "Y")
				{
					#
					# @todo - check prod_date_valid_from and prod_date_valid_to columns in later version
					#
					$product_id_list[$product_count++] = $cat_prod_row->product_id;
					array_push( $product_rows, $product );
				}
			}
			sort($product_id_list);
			$product_id_list = array_unique($product_id_list);
			if(sizeof($product_id_list) > $MAXPRODUCTS) break;
		}
		$list = "";
		$product_count = 0;
		foreach($product_id_list as $v)
		{
			$list .= " $v ";
			$pids[$product_count++] = $v;
		}
		$this->LogMsg("Available numbers are: $list ");

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
		if(sizeof($product_rows)>0)
		{
			$this->LogMsg("Picking products at random");
			$low_id = 0;
			$high_id = sizeof($pids)-1;

			if(sizeof($product_rows) < $MAXPRODUCTS)
			{
				$MAXPRODUCTS = sizeof($product_rows);
			}

			/*
			 * get products in random order and display
			 */
			do
			{
				$rand_idx = mt_rand($low_id, $high_id);
				$pid = $pids[ $rand_idx ];
				if(!in_array($pid, $selected))
				{
					$this->LogMsg("Product ID selected [ $pid ]");
					$row = $this->FindProduct( $product_rows, $pid );
					$image = '/media/product-image-missing.jpeg';
					$imagefile = $image;
					$path = $this->getStoragePath($pid);
					$found = 0;
					/*
					 * Find all images mapped to the product and select the one we can use on the store front
					 * This image was generated by our ResizeImage Job when the image was uplaoded.
					 */
					$product_images = ProdImageMap::where('product_id',$row->id)->get();
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
								$row->image = $imagefile;
								break;
							}
						}
						if($found) break;
					}
					if($found==0)
					{
						$row->image = $imagefile;
					}
	
					$cat_ids = CategoryProduct::where('product_id',$pid)->get();
	
					if(sizeof($cat_ids)>0) 
					{
						$row->category = Category::find($cat_ids[0]->category_id)->category_title;
					}
					else
						$row->category = "unknown";
					array_push($selected, $pid);
					array_push($products, $row);
				}
			} while (sizeof($selected) != sizeof($product_id_list));

			$attributes = Attribute::get();
			$attribute_values = AttributeValue::get();

			/*
			 * REFACTOR required, these should not be in this controller
			 */
			$size_attribute = Attribute::where('attribute_name','Size')->first();
			$sizes = AttributeValue::where('attr_id',$size_attribute->id)->get();
			$colour_attribute = Attribute::where('attribute_name','Colour')->first();
			$colours = AttributeValue::where('attr_id',$colour_attribute->id)->get();
			/*
			 * END
			 */

			$theme_path = \Config::get('THEME_HOME')."storefront";
			$this->LogMsg("Render View storefront from [".$theme_path."]");

			return view($theme_path,[
				'adverts'=>$this->getAdverts(),
				'categories'=>$categories,
				'settings'=>$settings,
				'products'=>$products,
				'attributes'=>$attributes,
				'attribute_values'=>$attribute_values,
				'sizes'=>$sizes,
				'colours'=>$colours
				]);
		}
		else
		{
			$size_attribute = Attribute::where('attribute_name','Size')->first();
			$sizes = AttributeValue::where('attr_id',$size_attribute->id)->get();
			$this->LogMsg("Render View Frontend.storefront - No Products assigned to shop");
			$theme_path = \Config::get('THEME_HOME')."storefront";
			return view($theme_path,[
				'settings'=>$settings,
				'sizes'=>$sizes,
				'categories'=>$categories,
				'adverts'=>$this->getAdverts(),
				'products'=>array()]);
		}
	}	



	/**
	 * If a menu is used to narrow products by size/colour etc
	 * then we are using the attributes table and attribute_products table
	 * to select products that have the required attribute (all "Small" sizes for instance).
	 *
	 * Use atriutes and parent products to return a collection of basic products.
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
				'current_store'=>$current_store,
				'settings'=>$settings ]);
		}
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
			$Image = new Image;
			$Category = new Category;
			$Product = new Product;
			$CategoryProduct  = new CategoryProduct;
			$AttributeProduct = new AttributeProduct;

			$store = app('store');
			$settings = StoreSetting::all();

			$related_products = array();
			$images = array();
			$thumbnails = array();
			if($id > 0)
			{
				$product = Product::where('id',$id)->first();
				##Amqp::publish('product_view', "{'product_id':'".$product->id."'}", ['exchange_type'=>'direct', 'exchange'=>'laravel', 'auto_delete'=>false]);
				$image_list = ProdImageMap::where('product_id',$product->id)->get();
				$this->LogMsg("Fetch images for this product");
				foreach($image_list as $i)
				{
					$this->LogMsg("Image [".$i->image_id."]");
					$row = Image::where('id',$i->image_id)->first();
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
				$attributes = Attribute::all();
				$product_attributes = AttributeProduct::where('product_id',$product->id)->get();
				switch($product->prod_type)
				{
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
	 * Category has been selected on a page so we need to return a view
	 * to show products from that category only.
	 * Collect all required data for view.
	 *
	 * @param	integer	$id	Row id from category table.
	 * @return	mixed	view to render
	 */
	public function ShowStoreCategory($id)
	{
		$this->LogFunction("ShowStoreCategory()");

#		$Product = new Product;
#		$Category = new Category;
#		$Image = new Image;
#		$CategoryProduct  = new CategoryProduct;

		$category_data = Category::find($id);
		#
		# {FIX_2018-04-03} If invalid category then trap here
		#
		$store = app('store');
		if(is_null($category_data))
		{
			$theme_path = \Config::get('THEME_ERRORS')."category-not-found";
			return view($theme_path,['store'=>$store]);
		}



		$size_attribute = Attribute::where('attribute_name','Size')->first();
		$sizes = AttributeValue::where('attr_id',$size_attribute->id)->get();

		$colour_attribute = Attribute::where('attribute_name','Colour')->first();
		$colours = AttributeValue::where('attr_id',$colour_attribute->id)->get();

		##Amqp::publish('category_view', "{'category_id':'".$category_data->id."'}", ['exchange_type'=>'direct', 'exchange'=>'laravel', 'auto_delete'=>false]);

		$current_store = app('store');
		$this->LogMsg("Current Store: [".$store->store_name."]" );
		/*
		 * Categories relevant to this shop
		 */	
		$categories = array();
		/*
		 * DB rows from
		 */	
		$product_rows = array();
		/*
		 * $product_id_list contains all product id's includes dupliactes. we need to remove duplicates later and sort list
		 */	
		$product_id_list = array();
		/*
		 * Product array passed to view
		 */
		$products = array();
		/*
		 * image array for products
		 */
		$images = array();

		if( is_null($current_store) == false)
		{
			$cat_prod_rows = CategoryProduct::where('category_id',$id)->get();
			#
			#
			#
			foreach($cat_prod_rows as $cat_prod_row)
			{
				$product = Product::where('id', $cat_prod_row->product_id)->first();
				if($product->prod_visible == "Y")
				{
					#
					# @todo - check prod_date_valid_from and prod_date_valid_to columns in later version
					#
					array_push( $product_id_list, $cat_prod_row->product_id );
					array_push( $product_rows, $product );
				}
			}
			sort($product_id_list);
			$product_id_list = array_unique($product_id_list);
	
	
			foreach($product_id_list as $product_id)
			{
				$this->LogMsg("Product ID selected [ $product_id ]");
				$row = $this->FindProduct( $product_rows, $product_id );
				$path = $this->getStoragePath($product_id);
	
				$row->category = $category_data->category_title;
				$row->category_id = $id;

				$found = 0;
				$product_images = ProdImageMap::where('product_id',$product_id)->get();
				$row->image = '/media/product-image-missing.jpeg';

				foreach($product_images as $prod_img_map_entry)
				{
					$id = array($prod_img_map_entry);
					$image_id = $id[0]->image_id;
					$image_row = Image::where('id',$image_id)->first();
					$row->image = "/".$image_row->image_folder_name."/".$image_row->image_file_name;
				}
				array_push($products,$row);
			}
			$theme_path = \Config::get('THEME_CATEGORY').'storecategorypage';
			return view($theme_path,[
				'store'=>$current_store,
				'sizes'=>$sizes,
				'colours'=>$colours,
				'category'=>$category_data,
				'categories'=>$this->GetStoreCategories($current_store->id),
				'adverts'=>$this->GetAdverts(),
				'products'=>$products
				]);
		}
		else
		{
			$theme_path = \Config::get('THEME_CATEGORY').'storecategorypage';
			return view($theme_path,[
				'store'=>$current_store,
				'sizes'=>$sizes,
				'colours'=>$colours,
				'category'=>$category_data,
				'categories'=>array(),
				'adverts'=>$this->GetAdverts(),
				'products'=>$products]);
		}
	}
	

	/**
	 * Insert into the "notifications" table the stock SKU and the persons email address
	 * Called via an AJAX call from user web page.
	 * and return a JSON string data with status code.
	 *
	 * email address = nf
	 * input field = sku
	 *
	 * POST ROUTE: /notify/outofstock
	 *
	 * @return	string
	 */
	public function OutOfStockNotify(Request $request)
	{
		$this->LogFunction("OutOfStockNotify() - AJAX Call");

		$rid = 0;
		if(Request::ajax())
		{
			$this->LogMsg("Process AJAX call");

			$form_data = Input::all();
			$email_address = $form_data['nf'];
			$sku = $form_data['sku'];

			$this->LogMsg("Captured [".$email_address."]");
			$this->LogMsg("Product: [".$sku."]");

			if(filter_var($email_address, FILTER_VALIDATE_EMAIL))
			{
				##Amqp::publish('subscribe_out_of_stock', "{'email_address':'".$email_address."','sku':'".$sku."'}", ['exchange_type'=>'direct', 'exchange'=>'laravel', 'auto_delete'=>false]);
				try
				{
					$o = new Notification;
					$o->date_created = date("Y-m-d");
					$o->time_created = date("H:i:s");
					$o->product_code = $sku;
					$o->email_address = $email_address;
					$o->save();
					$rid = $o->id;
				}
				catch(\Illuminate\Database\QueryException $ex)
				{
					$this->LogError("DB insert failed - row may already exist!");
				}
				$this->LogMsg("Row ID: [".$rid."]");
				if($rid>0)
				{
					$this->LogMsg("Return AJAX response");
					$data = array('status'=>'OK','S'=>'OK');
					return json_encode($data);
				}
				$this->LogMsg("Insert failed or user is present.");
			}
		}
		$this->LogMsg("return FAIL response.");
		$data = array('S'=>'FAIL', 'status'=>'FAIL');
		return json_encode($data);
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

		if(Request::ajax())
		{
			if(Auth::check())
			{
				$Product = new Product;
				$cart = Cart::where('user_id',Auth::user()->id)->first();
				if($cart)
				{
					$c = 0;
					$v = 0;
					$items = $cart->cartItems;
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
		$data = array('c'=>0,'v'=>0);
		return json_encode($data);
	}




	/**
	 * AJAX REQUEST - Capture the data from the pop up email/name capture form,
	 * save valid data in DB and return a JSON status code.
	 *
	 * @todo Customer Insert has been disabled while testing
	 *
	 * @return	string	JSON data
	 */
	public function CaptureForm()
	{
		$this->LogFunction("CaptureForm()");
		$store = app('store');
		if(Request::ajax())
		{
			$data_in = Input::all();
			$name = $data_in['na'];
			$emailaddress = $data_in['ea'];
			$this->LogMsg("Captured [".$name."]");
			$this->LogMsg("Captured [".$emailaddress."]");
			if((strlen($name)>1) && (strlen($emailaddress)>4))
			{
				$this->LogMsg("Get Customer Source Data");
				$source = CustSource::where('cs_name',"WEBSTORE")->first();
#				##Amqp::publish('subscribe', "{'email_address':'".$emailaddress."'}", ['exchange_type'=>'direct', 'exchange'=>'laravel', 'auto_delete'=>false]);

				$o = new Customer;
				$o->customer_name = $name;
				$o->customer_mobile = '';
				$o->customer_email = $emailaddress;
				$o->customer_source_id = $source->id;
				$o->customer_store_id = $store->id;
				$o->customer_status = 'A';
				$o->customer_date_created = date("Y-m-d");
				$o->customer_date_updated = date("Y-m-d");
				$o->save();
				$cid = $o->id;
				$this->LogMsg("Saved new customer  ID [".$o->id."]");
				$cmd = new ConfirmSubscription($store, $emailaddress );
				if(is_object($cmd))
				{
					dispatch($cmd);
				}
				Cookie::queue('capture','done',9999999);
				Cookie::queue('cid',"$cid",9999999);
				$data = array('status'=>'You have been subscribed!','CID'=>$cid);
				return json_encode($data);
			}
			else
			{
				$this->LogMsg("FAIL - AJAX call failed Name or Email address length failure");
				$data = array('status'=>'FAIL');
				return json_encode($data);
			}
		}
		else
		{
			$data = array('status'=>'Not AJAX Call');
			return json_encode($data);
		}
	}



	/**
	 *============================================================
	 *
	 *                        DEVELOPMENT
	 *
	 *============================================================
	 *
	 * Return a collection of rows that are current adverts from the adverts table.
	 *
	 * @return	mixed
	 */
	protected function GetAdverts()
	{
		$this->LogFunction("GetAdverts()");
		return Advert::where('advert_status','A')->get();
	}





	/**
	 * Return an array of formatted category objects, only the parent items.
	 *
	 * @return	array
	 */
	protected function GetStoreCategories($store_id = 0)
	{
		$this->LogFunction("GetStoreCategories(".$store_id.")");
		$category_data = Category::where('category_store_id',$store_id)->get();
		return $category_data;
	}




	/**
	 * Return the string represention of the category HTML unordered list
	 *
	 * @return	string
	 */
	protected function GetStoreCategoriesHTML()
	{
		$this->LogFunction("GetStoreCategoriesHTML()");

		$Category = new Category;
		return $Category->getHTML();
	}




	/**
	 * Construct path from ID and return
	 *
	 * @param	string	$str_id	String holding the ID value as a number
	 * @return	string
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

	



	/**
	 *
	 */
	public function getHomeCategory()
	{
		$this->LogFunction("getHomeCategory()");

		$category = new \stdClass;
		$category->category_description = "Storefront Home";
		return $category;
	}

	/**
	 * Remove all site cookies - primarily used for testing
	 *
	 * @return	string
	 */
	public function ClearCookies()
	{
		Cookie::queue( Cookie::forget('cid') );
		Cookie::queue( Cookie::forget('capture') );
		Session::forget('cid');
		Session::forget('capture');
		return ['ok'=>true];
	}

	/**
	 * Set capture done cookie and CID
	 *
	 * @return	string
	 */
	public function SetCookies(Request $request)
	{
		$cid = 0;
		$query = $request->input();
		foreach($query as $n=>$v)
		{
			if(is_string($n)== true)
			{
				if(is_string($v)== true)
				{
					if($n=="CID") $cid = $v;
				}
			}
		}
		if($cid > 0)
		{
			Cookie::queue('cid',$cid, 9999999);
			Cookie::queue('capture','done',9999999);
			return ['ok'=>true];
		}
		return ['ok'=>false];
	}



	/**
	 * Find the product in the array of product rows.
	 *
	 * @param	array	$products 	The array of retrieved products from the DB
	 * @param	integer	$id	The ID to find 
	 * @return	mixed
	 */
	protected function FindProduct($products, $id)
	{
		$product = array_filter($products, function($p) use ($id) { if($p['id'] == $id) { return $p;} });
		return reset($product);

		#foreach($products as $p)
		#{
		#if($p->id == $id) return $p;
		#}
		#return null;
	}



}
