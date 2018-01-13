<?php
/**
 * \class	Attributes
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2017-01-09
 * @package App\Models
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent Model to provide support for the "attributes" table.
 */
class Attributes extends Model
{
/**
 * Indicates if the model should be timestamped.
 *
 * @var bool $timestamps
 */
public $timestamps = false;




/**
 * The attributes that are mass assignable.
 *
 * @var array $fillable
 */
protected $fillable = ['attribute_name'];


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
		return \DB::table('attributes')->orderBy('id')->get();
	}



	/**
	 * Return a collection object with the rows given the store id
	 *
	 * @param	integer	$id		Store id from stores table
	 * @pre		None
	 * @post	None
	 * @return	mixed	Collection of rows
	 */
	public function getByStore($id)
	{
		return \DB::table('attributes')->where(['store_id'=>$id])->get();
	}



	/**
	 * Return a collection object with the rows given the attribute name
	 *
	 * @param	string	$name
	 * @return	mixed	Collection of rows
	 */
	public function getByName($name)
	{
		return \DB::table('attributes')->where(['attribute_name'=>$name])->get();
	}




	/**
	 * Insert a new row into the attributes table
	 *
	 * @param	string	$name
	 * @param	integer	$store_id
	 * @pre data array must have all columns as needed for insert
	 * @post new row inserted if no duplicate name and internal state updated
	 * @return integer row id
	 */
	public function InsertAttribute($name,$store_id)
	{
		$d = array('attribute_name'=>$name, 'store_id'=>$store_id);
		return \DB::table('attributes')->insertGetId( $d );
	}



	/**
	 * Update a row in the attributes table given the ID and data
	 *
	 * @param	array	$d
	 * @return	integer
	 */
	public function UpdateAttribute($d)
	{
		$id = $d['id'];
		return \DB::table('attributes')->where(['id'=>$this->id])->update(['attribute_name'=>$d['attribute_name']]);
	}



	/**
	 * Return a collection given the row ID
	 *
	 * @param	integer	$id row id
	 * @return	mixed
	 */
	public function getByID($id)
	{
		return \DB::table('attributes')->where(['id'=>$id])->first();
	}
}
