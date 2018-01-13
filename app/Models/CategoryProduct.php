<?php
/**
 * \class CategoryProduct
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2016-07-29
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
 * Tell framework thable is singular pivot table
 * @var string $table
 */
public $table="category_product";



/**
 * The row ID we are after
 * @var	integer	$id
 */
protected $id;



/**
 * The row id for the category table we reference to
 * @var integer $category_id
 */
protected $category_id;


/**
 * The row id for the product table we reference to.
 * @var integer $product_id
 */
protected $product_id;



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
	 * @return array - array of column names
	 */
	public function getFillable()
	{
		return $this->fillable;
	}




	/**
	 * @param $id - row id
	 * @return collection of row objects
	 */
	public function getByID($id)
	{
		return \DB::table('category_product')->where(['id'=>$id])->first();
	}




	/**
	 * Return a collection of rows of products this category is mapped to
	 *
	 * @param $id - row id from categories table
	 * @return collection of row objects
	 */
	public function getByCategoryID($id)
	{
		return \DB::table('category_product')->where(['category_id'=>$id])->orderBy('product_id')->get();
	}


	/**
	 * Return a collection of rows of categories this product is mapped to
	 *
	 * @param $id - row id from categories table
	 * @return collection of row objects
	 */
	public function getByProductID($id)
	{
		return \DB::table('category_product')->where(['product_id'=>$id])->orderBy('category_id')->get();
	}



	/**
	 *
	 * @param $cid - row id from categories table
	 * @param $pid - row di from products table
	 * @return integer - row ID of newly inserted row
	 */
	public function InsertCategoryProduct($cid,$pid)
	{
		$this->category_id = $cid;
		$this->product_id = $pid;
		$this->id = \DB::table('category_product')->insertGetId( array( 'category_id'=>$cid, 'product_id'=>$pid));
		return $this->id;
	}



	/**
	 * @param $id - row id
	 * @return integer number of affected rows
	 */
	public function DeleteByID($id)
	{
		return \DB::table('category_product')->where(['id'=>$id])->delete();
	}



	/**
	 * @param $id - row id
	 * @return integer number of affected rows
	 */
	public function DeleteByProductID($id)
	{
		return \DB::table('category_product')->where(['product_id'=>$id])->delete();
	}



	/**
	 * @param $id - row id
	 * @return integer number of affected rows
	 */
	public function DeleteByCategoryID($id)
	{
		return \DB::table('category_product')->where(['category_id'=>$id])->delete();
	}
}
