<?php
/**
 * \class	TemplateActions
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-18
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief A list of Valid Actions (Business Processes) that we have templates for.
 *
 * See TemplateMapping for information on their use.
 *
 * Note that action and store_id are a unique index
 */
class TemplateActions extends Model
{



/**
 * The row id from the database table template_actions
 * @var int $id
 */
protected $id;


/**
 * The business process action name in UPPERCASE
 * @var string $action_name
 */
protected $action_name;


/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['action_name'];





	/**
	 * Return all rows from the template_actions table.
	 *
	 * @return	mixed
	 */
	public function getAll()
	{
		return \DB::table('template_actions')->orderBy('id')->get();
	}






	/**
	 * return all rows as a PHP array
	 *
	 * @return	array
	 */
	public function getAsArray()
	{
		$rows = \DB::table('template_actions')->orderBy('id')->get();
		$result = array();
		$result[0]="Not Implemented";

		foreach($rows as $r)
		{
			$result[$r->id] = $r->action_name;
		}
		return $result;
	}





	/**
	 * Given a row ID value, get all rows from the template_actions table.
	 *
	 * @param	integer	$id	The Row ID.
	 * @return	mixed	Collection of rows
	 */
	public function getByID($id)
	{
		return \DB::table('template_actions')->where(['id'=>$id])->first();
	}



	/**
	 * Return a HTML select list of the template actions.
	 * if specified, enable a specific value using the row ID
	 *
	 * @param	integer	$default	Row ID to flag as the seelcted item
	 * @return	string	HTML text
	 */
	public function getHTML($default=0)
	{
		$rows = \DB::table('template_actions')->orderBy('id')->get();
		$html = "<select class=\"form-control\" id=\"actions\" name=\"actions\">\n";
		foreach($rows as $r)
		{
			$sel = ($r->id==$default)?" selected ":"";
			$html.= "<option value=\"".$r->id."\" $sel >".$r->action_name."</option>\n";
		}
		$html.="</select>\n";
		return $html;
	}



	/**
	 * Insert a new action into template_actions table
	 *
	 * @pre name must be defined and not already present in the DB
	 * @post new row inserted into DB table tample_actions
	 *
	 * @param	string	$str	The action name we are going to insert
	 * @return 	integer	The newly insert row ID
	 */
	public function InsertAction($str)
	{
		$this->action_name = strtoupper($str);
		$this->id = \DB::table('template_actions')->insertGetId(array('action_name'=>$this->action_name));
		return $this->id;
	}
}
