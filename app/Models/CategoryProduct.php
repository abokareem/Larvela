<?php
/**
 * \class	CategoryProduct
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 *
 *
 *
 *
 *  [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Pivot table for category_product mapping, was called prod_cat_maps
 *
 * 2016-09-22 Renamed table from prod_cat_maps to category_product
 */
class CategoryProduct extends Model
{


/**
 * Tell framework the table is singular pivot table
 * @var string $table
 */
public $table="category_product";



/**
 * Disable framework from timestamping rows
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * Array of columns that are mass assignable
 * @var array $fillable
 */
protected $fillable = array('category_id','product_id');








	/**
	 * Returns all relevant product_id rows
	 *
	 * @return  mixed
	 */
	public function products()
	{
		return $this->belongsTo('App\Models\Product');
	}
}
