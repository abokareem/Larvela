<?php
/**
 * \class	Seo
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-07-29
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model Contains CRUD methods for the "seo" table.
 *
 * {FIX_2017-10-25} Model Seo.php -Removed getByID call
 */
class Seo extends Model
{



/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * Name of databasetable needed by Eloquent calls
 * {FIX_2017-10-25} Model Seo.php - Added table name variable
 * @var string $table
 */
protected $table = "seo";


/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
public $fillable =['seo_token','seo_html_data'.'seo_status','seo_store_id','seo_edit'];


	#\Schema::getColumnListing('seo'));



	/**
	 * Insert a row of data given an array of row data.
	 *
	 * @pre		Data rows must be defined 
	 * @post	New row insert, internal state updated
	 * @param	array	$d
	 * @return	integer
	 */
	public function InsertSeo($d)
	{
		return  \DB::table('seo')->insertGetId(
			array(
				'seo_token'    => $d['seo_token'],
				'seo_html_data'=> $d['seo_html_data'],
				'seo_status'   => $d['seo_status'],
				'seo_store_id' => $d['seo_store_id'],
				'seo_edit'     => $d['seo_edit']
				)
			);
	}




	/**
	 * Basic CRUD fucntion to update an existing row given the ID and data
	 *
	 * @pre		Data rows must be defined 
	 * @post	Existing row is updated for the given row ID.
	 * @param	array	$d
	 * @return	integer
	 */
	public function UpdateSeo($d)
	{
		return \DB::table('seo')->where(['id'=>$d['id'] ])->update(
			array(
				'seo_token'=>$d['seo_token'],
				'seo_html_data'=>$d['seo_html_data'],
				'seo_status'=>$d['seo_status'],
				'seo_edit'=>$d['seo_edit'],
				'seo_store_id'=>$d['seo_store_id']
				));
	}




	/**
	 * Return all rows from the table from the "seo" table given the row ID.
	 *
	 * @pre		None
	 * @post	None
	 * @param	integer	$id The row ID
	 * @return	mixed
	 */
#	public function getByID($id)
#	{
#		return \DB::table('seo')->where(['id'=>$id])->first();
#	}




	/**
	 * Return a row from the table "seo" given the store ID and token name
	 *
	 * @pre		None
	 * @post	None
	 * @param	integer	 $sid The store ID
	 * @param	string	 $token
	 * @return	mixed
	 */
	public function getByStoreToken($sid,$token)
	{
		return \DB::table('seo')
			->where(['seo_store_id'=>$sid])
			->where(['seo_token'=>$token])
			->get();
	}
	
	
	
	/**
	 * Return all rows from the table "seo".
	 *
	 * @pre		None
	 * @post	None
	 * @return	mixed
	 */
	public function getAllRows()
	{
		return \DB::table('seo')->get();
	}
}
