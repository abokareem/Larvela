<?php
/**
 * \class	FindProducts
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-09-11
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
 */
namespace App\Services;

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
use App\Models\CategoryImage;


use App\Traits\Logger;


/**
 * \brief Generates the products that will appear on the Store Front page view.
 *
 * {INFO_2018-09-12} "FindProducts.php" - Removed from Store Front Controller and made into a service.
 */
class FindProducts
{
use Logger;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("store");
		$this->setClassName("FindProducts");
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
	 * Generate a range of random products for the current
	 * store that meet the following (selectable) criterior:
	 * - In Stock
	 * - New (within X days), 0 = disabled (any)
	 * - Specific Category, 0 = disabled (any category that is active)
	 *
	 * @param	integer	$MAX_PRODUCTS	Count of products to show on the home page
	 * @return	mixed	View object with data
	 */
	public function ReturnProducts( $IN_STOCK=1,$RANDOMIZE=1, $NEW_PRODUCT_DAYS=0, $IN_CATEGORY=0, $MAX_PRODUCTS = 90)
	{
		$this->LogFunction("returnProducts()");

		$store = app('store');

		$categories = array();
		$product_rows = array();
		$product_id_list = array();
		$pids = array();

		$Category = new Category;
		$Product = new Product;
		$CategoryProduct = new CategoryProduct;
		#
		# Need to get a sample of products relevant to this store
		# and limit them so the visitor can drill down by category.
		#
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$this->LogMsg("Current Store: ".$store->store_name );
		$this->LogMsg("There are [".sizeof($settings)."] settings specific to this Store.");
		$this->LogMsg("Flags: IN_STOCK [".$IN_STOCK."]");
		$this->LogMsg("Flags: NEW_PRODUCT_DAYS [".$NEW_PRODUCT_DAYS."]");
		$this->LogMsg("Flags: IN_CATEGORY [".$IN_CATEGORY."]");
		$this->LogMsg("Flags: MAX_PRODUCTS [".$MAX_PRODUCTS."]");
		$this->LogMsg("Flags: RANDOMIZE [".$RANDOMIZE."]");
		if($store != NULL)
		{
			if($IN_CATEGORY==0)
			{
				$this->LogMsg("Load categories for store");
				$categories = Category::where('category_store_id',$current_store->id)->get();
			}
			else
			{
				$this->LogMsg("Load category [".$IN_CATEGORY."]");
				$categories = Category::where('id',$IN_CATEGORY)->get();
			}
		}
		else
		{
			$this->LogError("Store data not loaded - no categories available");
			return array();
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
				if(($product->prod_visible == "Y")&&($product->prod_qty>0))
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
			if(sizeof($product_id_list) > $MAX_PRODUCTS) break;
		}
		$product_count = 0;
		foreach($product_id_list as $v)
		{
			$pids[$product_count++] = $v;
		}
		$list = array_reduce($product_id_list, function($list, $item) { $list .= " $item "; return $list; });
		$this->LogMsg("Available numbers are: $list ");

		/*
		 * Select products in random order using the product_id_list as our range.
		 * Scan through product_rows to find item by ID as its NOT in order.
		 */

		/*
		 * Use "selected" to keep check of a product we have already picked out of the list
		 */
		$selected = array();
		$products = array();
		if(sizeof($product_rows)>0)
		{
			$this->LogMsg("Picking products at random");
			$low_id = 0;
			$high_id = sizeof($pids)-1;

			if(sizeof($product_rows) < $MAX_PRODUCTS)
			{
				$MAX_PRODUCTS = sizeof($product_rows);
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
		}
		return $products;
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
	}



}
