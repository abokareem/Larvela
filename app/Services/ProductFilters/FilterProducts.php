<?php
/**
 * \class	FilterProducts
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-13
 * \version	1.0.3
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
namespace App\Services\ProductFilters;

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

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\CustSource;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Store;
use App\Models\StoreSetting;


use App\Traits\Logger;


/**
 * \brief Generates the products that will appear on the Store Front page view.
 *
 * {INFO_2018-09-12} "FilterProducts.php" - Removed from Store Front Controller and made into a service.
 */
class FilterProducts
{
use Logger;

private $state = 0;
private $in_stock_only = 0;
private $required_categories = array();
private $excluded_categories = array();
private $return_max_count = 0;
private $return_random = 1;
private $return_lowest_price_first = 0;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("store");
		$this->setClassName("FilterProducts");
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
	 * Set the flag during PRE-PROCESSING state.
	 *
	 * @param	integer	$state
	 * @param	mixed	$setting
	 * @param	array	$results
	 * @return	array
	 */
	protected function in_stock_only($state, $setting, $results)
	{
		if($state ==0)
		{
			$this->in_stock_only = $setting->setting_value;
		}
		return $results;
	}



	/**
	 * Save the required category in an array.
	 * - can have multiple entries in final array.
	 *
	 * @param	integer	$state
	 * @param	mixed	$setting
	 * @param	array	$results
	 * @return	array
	 */
	protected function display_category($state, $setting, $results)
	{
		if($state!=0) return $results;
		array_push($this->required_categories, $setting->setting_value);
		return $results;
	}



	/**
	 * Categories to exclude in PROCESSING state.
	 *
	 * @param	integer	$state
	 * @param	mixed	$setting
	 * @param	array	$results
	 * @return	array
	 */
	protected function exclude_category($state, $setting, $results)
	{
		if($state!=0) return $results;
		array_push($this->excluded_categories, $setting->setting_value);
		return $results;
	}


	






	/**
	 * Find a class method with the same name and call it.
	 * - Pass in the state and variables so it can decide what to do.
	 *
	 * @param	integer	$state
	 * @param	mixed	$setting
	 * @param	array	$results
	 * @return	array
	 */
	protected function ProcessState($state, $setting, $results)
	{
		$this->LogFunction("ProcessState()");
		$methods = get_class_methods($this);
		foreach($methods as $m)
		{
			if($m == strtolower($setting->setting_name))
			{
				$this->LogMsg("Process [".$m."]");
				$results = $this->{$m}($state, $setting, $results);
			}
		}
		return $results;
	}



	protected function Process($state,$setting,$result)
	{
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
	public function ReturnProducts(
		$IN_STOCK=1,
		$RANDOMIZE=1,
		$NEW_PRODUCT_DAYS=0,
		$IN_CATEGORY=0,
		$MAX_PRODUCTS = 90)
	{
		$this->LogFunction("ReturnProducts()");

		$result = array();
		$store = app('store');
		$s = new StoreSetting;
		$s->setting_name = "Process";
		$s->setting_value=1;
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$settings->push($s);
		for($state = 0; $state<= 2; $state++)
		{
			foreach($settings as $setting)
			{
				$result = $this->ProcessState($state,$setting,$result);
			}
		}
	}



	public function XXXX()
	{


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
				$categories = Category::where('category_store_id',$store->id)->get();
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
	}






	/**
	 * Find the product in the array of product rows.
	 *
	 * @param	array	$products 	The array of retrieved products from the DB
	 * @param	integer	$id	The ID to find 
	 * @return	mixed
	 */
	protected function ProductFinder($products, $id)
	{
		$product = array_filter($products, function($p) use ($id) { if($p['id'] == $id) { return $p;} });
		return reset($product);
	}



}
