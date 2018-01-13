<?php
/**
 * \class	Product
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-07-29
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide all CRUD functionality for the "products" table.
 */
class Product extends Model
{


protected $table = "products";


public $timestamps = false;


protected $fillable = ['prod_sku','prod_title','prod_short_desc','prod_long_desc','prod_visible','prod_weight','prod_qty','prod_reorder_qty','prod_base_cost','prod_retail_cost','prod_combine_code','prod_date_created','prod_time_created','prod_date_updated','prod_time_updated','prod_date_valid_from','prod_date_valid_to','prod_type'];


	/**
	 *==================================================
	 *                  DEVELOPMENT
	 *==================================================
	 *
	 * Return a list of columns for product attributes (colour etc).
	 * still needs refinement.
	 * @return	array
	 */
	public function getChildProducts($id)
	{
		$product = \DB::table('products')->where(['id'=>$id])->first();
		$sku = $product->prod_sku;
		$attr_list = array();
		$attributes = \DB::table('attribute_product')->where(['product_id'=>$id])->get();

		foreach( $attributes as $a)
		{
			echo "<br>";
			print_r($a);
			array_push($attr_list, $a->attribute_id);
		}
		$attr_rows = \DB::table('attribute_values')->whereIn('attr_id', $attr_list)->orderBy('attr_sort_index')->get();
		foreach($attr_rows as $ar)
		{
			$child_sku = $sku."-".$ar->attr_value;
			echo "<p>".$child_sku."</p>";
		}

		dd($attr_rows);
		#
		# we now have the attribute (i.e. SIZE, COLOUR in ID order)
		# Construct CHILD products using the parent SKU and add each attribute SIZE-COLOUR
	}





	/**
	 * Return a list of columns
	 *
	 * @return	array
	 */
	public function getFillable()
	{
		return $this->fillable;
	}


	/**
	 * Return a list of products which are visible.
	 *
	 * @return	mixed
	 */
	public function getAllActive()
	{
		return \DB::table('products')
			->where(['prod_visible'=>'Y'])
			->orderBy('id')
			->get();
	}



	/**
	 * Product is assigned to 1 or more categories which are assigned to store(s).
	 * Get all categories for this store, the get all product assigned to the categories.
	 *
	 * @param	integer	$store_id	
	 * @return	mixed
	 */
	public function getProductsByStore($store_id)
	{
		$Category = new Category;
		$categories = $Category->getByStoreID($store_id);
		$product_list = array();
		foreach($categories as $c)
		{
			$products = \DB::table('category_product')->where(['category_id'=>$c->id])->get();
			foreach( $products as $p)
			{
				array_push($product_list, $p->product_id);
			}
		}
		return \DB::table('products')->whereIn('id', $product_list)->orderBy('prod_sku')->get();
	}



	/**
	 * Product is assigned to 1 or more categories which are assigned to store(s).
	 * Get all categories for this store, the get all product assigned to the categories.
	 *
	 * @param	integer	$store_id	
	 * @return	mixed
	 */
	public function getProductsByCategory($category_id)
	{
		$product_list = array();
		$products = \DB::table('category_product')->where(['category_id'=>$category_id])->get();
		foreach( $products as $p)
		{
			array_push($product_list, $p->product_id);
		}
		return \DB::table('products')->whereIn('id', $product_list)->orderBy('prod_sku')->get();
	}


	/**
	 *
	 * @return	mixed
	 */
	public function getProducts()
	{
		return \DB::table('products')->get();
	}



	public function searchBySku($sku)
	{
		$s = $sku."%";
		return \DB::table('products')->where("prod_sku","LIKE", $s)->orderBy("prod_sku")->get();
	}


	/**
	 * Retrieve active records by combine code
	 *
	 * @param	string	$cc
	 * @return	mixed
	 */
	public function getByCombineCode($cc)
	{
		return \DB::table('products')->where(['prod_combine_code'=>$cc])->orderBy("prod_weight")->get();
	}







	public function InsertProduct($d)
	{
		return \DB::table('products')->insertGetId(
			array(
				'prod_sku'       =>$d['prod_sku'],
				'prod_title'     =>$d['prod_title'],
				'prod_short_desc'=>$d['prod_short_desc'],
				'prod_long_desc' =>$d['prod_long_desc'],
				'prod_visible'   =>$d['prod_visible'],
				'prod_weight'    =>$d['prod_weight'],
				'prod_qty'       =>$d['prod_qty'],
				'prod_reorder_qty' =>$d['prod_reorder_qty'],
				'prod_base_cost'   =>$d['prod_base_cost'],
				'prod_retail_cost' =>$d['prod_retail_cost'],
				'prod_combine_code'=>$d['prod_combine_code'],
				'prod_date_created'=>date("Y-m-d"),
				'prod_time_created'=>date("H:i:s"),
				'prod_date_updated'=>date("Y-m-d"),
				'prod_time_updated'=>date("H:i:s"),
				'prod_date_valid_from'=>$d['prod_date_valid_from'],
				'prod_date_valid_to'  =>$d['prod_date_valid_to'],
				'prod_type'  =>$d['prod_type']
			));
	}


	/**
	 *
	 *
	 *
	 * @param	array	$d
	 * @return	integer
	 */
	public function UpdateProduct($d)
	{
		return \DB::table('products')->where(['id'=>$d['id'] ])->update([
			'prod_sku'=>$d['prod_sku'],
			'prod_title'=>$d['prod_title'],
			'prod_short_desc'=>$d['prod_short_desc'],
			'prod_long_desc'=>$d['prod_long_desc'],
			'prod_visible'=>$d['prod_visible'],
			'prod_weight'=>$d['prod_weight'],
			'prod_qty'=>$d['prod_qty'],
			'prod_reorder_qty' =>$d['prod_reorder_qty'],
			'prod_base_cost'   =>$d['prod_base_cost'],
			'prod_retail_cost' =>$d['prod_retail_cost'],
			'prod_combine_code'   =>$d['prod_combine_code'],
			'prod_date_updated'   =>$d['prod_date_updated'],
			'prod_time_updated'   =>$d['prod_time_updated'],
			'prod_date_valid_from'=>$d['prod_date_valid_from'],
			'prod_date_valid_to'  =>$d['prod_date_valid_to'],
			'prod_type'  =>$d['prod_type']
			]);
	}






	/**
	 * given the product sku, update the qty
	 *
	 *
	 * @param	string	$sku
	 * @param	integer	$qty
	 * @return	integer
	 */
	public function UpdateQtyBySku($sku, $qty)
	{
		$today = date("Y-m-d");
		$now = date("H:i:s");
		return \DB::table('products')->where(['prod_sku'=>$sku])->update([
			'prod_qty'=>$qty,
			'prod_date_updated'=>$today,
			'prod_time_updated'=>$now
			]); 
	}





	/**
	 * Given the product id, update the qty
	 *
	 *
	 * @param	integer	$id
	 * @param	integer	$qty
	 * @return	integer
	 */
	public function UpdateQty($id, $qty)
	{
		$today = date("Y-m-d");
		$now = date("H:i:s");
		return \DB::table('products')->where(['id'=>$id])->update([
			'prod_qty'=>$qty,
			'prod_date_updated'=>$today,
			'prod_time_updated'=>$now
			]); 
	}






	public function images()
	{
		return $this->belongsToMany('App\Models\Images');
	}



	public function categories()
	{
		return $this->blongsToMany('App\Categories');
	}


	
	/**
	 * Given a text string containing codes, and a producte object containg row data
	 * return a text string with the codes translated into data.
	 *
	 * @param	string	$text
	 * @param	mixed	$obj
	 * @return	string
	 */
	public function translate($text,$obj)
	{
		$translations = array( 
			"{PRODUCT_ID}"=>$obj->id,
			"{PRODUCT_SKU}"=>$obj->prod_sku,
			"{PRODUCT_TITLE}"=>$obj->prod_title,
			"{PRODUCT_NAME}"=>$obj->prod_title,
			"{PRODUCT_SHORT_DESC}"=>$obj->prod_short_desc,
			"{PRODUCT_LONG_DESC}"=>$obj->prod_long_desc,
			"{PRODUCT_WEIGHT}"=>$obj->prod_weight,
			"{PRODUCT_QTY}"=>$obj->prod_qty,
			"{PRODUCT_REORDER_QTY}"=>$obj->prod_reorder_qty,
			"{PRODUCT_BASE_COST}"=>$obj->prod_base_cost,
			"{PRODUCT_RETAIL_COST}"=>$obj->prod_retail_cost,
			"{PRODUCT_COMBINE_CODE}"=>$obj->prod_combine_code,
			"{PRODUCT_DATE_CREATED}"=>$obj->prod_date_created,
			"{PRODUCT_DATE_UPDATED}"=>$obj->prod_date_updated,
			"{PRODUCT_TIME_CREATED}"=>$obj->prod_time_created,
			"{PRODUCT_TIME_UPDATED}"=>$obj->prod_time_updated,
			"{PRODUCT_DATE_VALID_FROM}"=>$obj->prod_date_valid_from,
			"{PRODUCT_DATE_VALID_TO}"=>$obj->prod_date_valid_to
			);
		return str_replace(array_keys($translations), array_values($translations), $text);
	}
}
