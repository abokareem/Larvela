<?php
/**
 * \class TemplateMappings
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2016-08-18
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Map the template file name to a template "action" (business process) for the relevant store.
 *
 * Maps to a template action (using the full name) to the template for the store. 
 * Normally we would use template ID value and template name.
 * Note that action and store_id are a unique index in the table.
 */
class TemplateMappings extends Model
{


/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;



/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['template_name','template_action_id','template_store_id'];


	/**
	 * Insert a single row into the DB table template_mapping
	 *
	 * @param 	array	$d	array of column data
	 * @return	int		The newly inserted row id 
	 */
	public function InsertMapping($d)
	{
		$this->template_name = $d['template_name'];
		$this->template_action_id = $d['template_action_id'];
		$this->template_store_id = $d['template_store_id'];

		$this->id = \DB::table('template_mapping')->insertGetId( array(
			'template_name'=>$d['template_name'],
			'template_action_id'=>$d['template_action_id'],
			'template_store_id'=>$d['template_store_id']
			));
		return $this->id;
	}


	/**
	 * Return all mapping rows as a collection
	 *
	 * @return	mixed
	 */
	public function getAll()
	{
		return \DB::table('template_mapping')->get();
	}




	/**
	 * Given a row id, fetch and return a Collection object.
	 * The icollection returned should be a single row indexed at 0.
	 *
	 * @param	integer	$id	The row ID
	 * @return	mixed	Collection object zero indexed
	 */
	public function getByID($id)
	{
		return \DB::table('template_mapping')->where(['id'=>$id])->first();
	}



	/**
	 * Return a Collection object of the rows that match the store ID.
	 *
	 * @param	integer	$id The row ID of the store
	 * @return	mixed	Collection object zero indexed
	 */
	public function getByStoreID($id)
	{
		$this->template_store_id = $id;
		$rows = \DB::table('template_mapping')->where(['template_store_id'=>$id])->orderBy('id')->get();
		foreach($rows as $r)
		{
			$this->id = $r->id;
			$this->template_name = $r->template_name;
			$this->template_action_id = $r->template_action_id;
		}
		return $rows;
	}




	/**
	 * Delete the specific row using the action and store ID's (should always be zero or 1 row).
	 *
	 * @param	integer	$aid	row id of the action from the template_actions table
	 * @param	integer	$sid	row id of the store 
	 * @return	integer	The number of rows deleted
	 */
	public function DeleteByAIDSID($aid, $sid)
	{
		return \DB::table('template_mapping')
			->where(['template_store_id'=>$sid])
			->where(['template_action_id'=>$aid])
			->delete();
	}
}
