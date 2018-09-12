<?php
/**
 * \class	CategoryPageController
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

use App\Models\Advert;
use App\Models\Attribute;
use App\Models\AttributeProduct;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\CategoryProduct;
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
 * {INFO_2018-09-12} "CategoryPageController.php" - Created from StoreFrontController code.
 */
class CategoryPageController extends Controller
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
		$this->setClassName("CategoryPageController");
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

		$Category = new Category;
		$images = $Category->images;

		$category = Category::find($id);
		#
		# {FIX_2018-04-03} If invalid category then trap here
		#
		$store = app('store');
		if(is_null($category))
		{
			$theme_path = \Config::get('THEME_ERRORS')."category-not-found";
			return view($theme_path,['store'=>$store]);
		}

		$attributes = Attribute::where('store_id',$store->id)->get()->toArray();
		#
		# @todo Remove specific attributes and just pass attributes for  this store into the theme page.
		#
		$size_attribute = Attribute::where('attribute_name','Size')->first();
		$sizes = AttributeValue::where('attr_id',$size_attribute->id)->get();

		$colour_attribute = Attribute::where('attribute_name','Colour')->first();
		$colours = AttributeValue::where('attr_id',$colour_attribute->id)->get();


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
			#$cat_prod_rows = $category->products;
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
	
				$row->category = $category->category_title;
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
				'attributes'=>$attributes,
				'sizes'=>$sizes,
				'colours'=>$colours,
				'category'=>$category,
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
				'category'=>$category,
				'categories'=>array(),
				'adverts'=>$this->GetAdverts(),
				'products'=>$products]);
		}
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



}
