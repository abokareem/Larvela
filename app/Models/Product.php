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

/**
 * The table this model uses
 * @var string $table
 */
protected $table = "products";


/**
 * The table this model uses
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * columns that are mass-assignable
 * @var string $fillable
 */
protected $fillable = ['prod_sku','prod_title','prod_short_desc','prod_long_desc','prod_visible','prod_weight','prod_qty','prod_reorder_qty','prod_base_cost','prod_retail_cost','prod_combine_code','prod_date_created','prod_time_created','prod_date_updated','prod_time_updated','prod_date_valid_from','prod_date_valid_to','prod_type'];


#	public function InsertProduct($d)
#	{
#		return \DB::table('products')->insertGetId(
#			array(
#				'prod_sku'       =>$d['prod_sku'],
#				'prod_title'     =>$d['prod_title'],
#				'prod_short_desc'=>$d['prod_short_desc'],
#				'prod_long_desc' =>$d['prod_long_desc'],
#				'prod_visible'   =>$d['prod_visible'],
#				'prod_weight'    =>$d['prod_weight'],
#				'prod_qty'       =>$d['prod_qty'],
#				'prod_reorder_qty' =>$d['prod_reorder_qty'],
#				'prod_base_cost'   =>$d['prod_base_cost'],
#				'prod_retail_cost' =>$d['prod_retail_cost'],
#				'prod_combine_code'=>$d['prod_combine_code'],
#				'prod_date_created'=>date("Y-m-d"),
#				'prod_time_created'=>date("H:i:s"),
#				'prod_date_updated'=>date("Y-m-d"),
#				'prod_time_updated'=>date("H:i:s"),
#				'prod_date_valid_from'=>$d['prod_date_valid_from'],
#				'prod_date_valid_to'  =>$d['prod_date_valid_to'],
#				'prod_type'  =>$d['prod_type']
#			));
#	}
#
#
#	/**
#	 *
#	 *
#	 *
#	 * @param	array	$d
#	 * @return	integer
#	 */
#	public function UpdateProduct($d)
#	{
#		return \DB::table('products')->where(['id'=>$d['id'] ])->update([
#			'prod_sku'=>$d['prod_sku'],
#			'prod_title'=>$d['prod_title'],
#			'prod_short_desc'=>$d['prod_short_desc'],
#			'prod_long_desc'=>$d['prod_long_desc'],
#			'prod_visible'=>$d['prod_visible'],
#			'prod_weight'=>$d['prod_weight'],
#			'prod_qty'=>$d['prod_qty'],
#			'prod_reorder_qty' =>$d['prod_reorder_qty'],
#			'prod_base_cost'   =>$d['prod_base_cost'],
#			'prod_retail_cost' =>$d['prod_retail_cost'],
#			'prod_combine_code'   =>$d['prod_combine_code'],
#			'prod_date_updated'   =>$d['prod_date_updated'],
#			'prod_time_updated'   =>$d['prod_time_updated'],
#			'prod_date_valid_from'=>$d['prod_date_valid_from'],
#			'prod_date_valid_to'  =>$d['prod_date_valid_to'],
#			'prod_type'  =>$d['prod_type']
#			]);
#	}
#







	/**
	 * Get all images from the image_product pivot table
	 * returns an Eloquent Collection Object
	 *
	 * Usage:  $product->images()->get();
	 *
	 * @return	mixed
	 */
	public function images()
	{
		return $this->belongsToMany('App\Models\Image');
	}



	public function categories()
	{
		return $this->belongsToMany('App\Models\Category');
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
