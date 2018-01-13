<?php
/**
 * \class	AttributeProduct
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-01-11
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent Model to provide support for the "attribute_product" table.
 */
class AttributeProduct extends Model
{

/**
 * Define the table name specifically
 * @var string $table
 */
protected $table = "attribute_product";

/**
 * Indicates if the model should be timestamped.
 * @var boolean $timestamps
 */
public $timestamps = false;




/**
 * The attribute_product that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['attribute_id','product_id'];


	/**
	 * Print the items that are mass assignable
	 *
	 * @return	array
	 */
	public function getFillable() { return $this->fillable; }



	/**
	 * Return a collection of all rows ordered by the row id.
	 *
	 * @return	mixed	Collection of rows
	 */
	public function getAll()
	{
		return \DB::table('attribute_product')->orderBy('id')->get();
	}




	/**
	 * Return a collection object with the rows given the attribute id
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function getByAID($id)
	{
		return \DB::table('attribute_product')->where(['attribute_id'=>$id])->get();
	}




	/**
	 * Return a collection object with the rows given the product id
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function getByPID($id)
	{
		return \DB::table('attribute_product')->where(['product_id'=>$id])->get();
	}




	/**
	 * Insert a new row into the attribute_product table and return the row id
	 *
	 * @param	integer	$aid
	 * @param	integer	$pid
	 * @return	integer 
	 */
	public function InsertAttribute($aid,$pid)
	{
		$d = array('attribute_id'=>$aid, 'product_id'=>$pid);
		return \DB::table('attribute_product')->insertGetId( $d );
	}



	/**
	 * Update a row in the attribute_product table given the ID and data
	 *
	 * @param	array	$d
	 * @return	integer
	 */
	public function UpdateAttribute($d)
	{
		return \DB::table('attribute_product')->where(['id'=>$d['id']])->update( $d );
	}



	/**
	 * Return a collection given the row ID
	 *
	 * @param	integer	$id row id
	 * @return	mixed
	 */
	public function getByID($id)
	{
		return \DB::table('attribute_product')->where(['id'=>$id])->first();
	}
}
