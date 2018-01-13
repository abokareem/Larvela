<?php
/**
 * \class	StoreSettings
 * @date	2016-12-15
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Eloquent model for the "store_settings" table.
 */
class StoreSettings extends Model
{


/**
 * @var string $table;
 */
protected $table="store_settings";


/**
 * @var boolean $timestamps
 */
public $timestamps= false;


/**
 * The items that are mass assignable
 * @var array $fillable
 */
protected $fillable = array('setting_name','setting_value','setting_store_id');

	/**
	 * Get all rows given a store ID
	 *
	 * param	integer	$id	Store id
	 * @return	mixed
	 */
	public function getByStoreID($id)
	{
		return StoreSettings::where('setting_store_id', $id)->orderBy('setting_name')->get();
	}


	/**
	 * Get all rows given a row ID
	 *
	 * param	integer	$id	row id
	 * @return	mixed
	 */
	public function getByID($id)
	{
		return StoreSettings::where(['id'=>$id])->first();
	}




	public function InsertStoreSetting($d)
	{
		return \DB::table('store_settings')->insertGetId( $d );
	}



	public function UpdateStoreSetting($d)
	{
		return \DB::table('store_settings')->where(['id'=>$d['id']])->update( $d );
	}
}
