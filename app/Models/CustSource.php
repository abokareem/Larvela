<?php
/**
 * \class	CustSource
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 *
 *
 *  [CC]
 */
namespace App\Models;



use Illuminate\Database\Eloquent\Model;


/**
 * \brief Model for managing the Customer Referral Source
 * i.e. WEBSITE, EBAY etc
 */
class CustSource extends Model
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
public $fillable =['cs_name'];


	/**
	 * Insert a row of data using the raw data, not an array as usual
	 *
	 * @param	string	$cs_name	The name of the referral to insert
	 * @return	integer
	 */
	public function InsertSource($cs_name)
	{
		return \DB::table('cust_sources')->insertGetId( array('cs_name'=>$cs_name) );
	}



	/**
	 * Given the row ID, return the row
	 *
	 * @param	integer	$id	The row ID
	 * @return	mixed database rows
	 */
	public function getByID($id)
	{
		return \DB::table('cust_sources')->where(['id'=>$id])->first();
	}
	


	/**
	 * Given the name of the referral source, return the row.
	 *
	 * @param	string	$name	The name of the referral
	 * @return	mixed database rows
	 */
	public function getByName($name)
	{
		return \DB::table('cust_sources')->where(['cs_name'=>$name])->first();
	}
	
	

	/**
	 * Return all rows from the data base as a Laravel Collection object.
	 *
	 * @return	mixed	database rows
	 */
	public function getAllRows()
	{
		return \DB::table('cust_sources')->get();
	}



	/**
	 * Return all rows as an array from the table.
	 *
	 * @return	array
	 */
	public function getArray()
	{
		$rows = $this->getAllRows();
		$data = array();
		$data[0]="Manually Entered";
		foreach($rows as $row)
		{
			$data[$row->id] = $row->cs_name;
		}
		return $data;
	}



	/**
	 * Generic HTML select list of sorted stores.
	 *
	 * @param	string	$name	HTML Name of select list, default is "store_id"
	 * @param	integer	$id		row id to mark as selected, if not specified AND not global then first item is "Please select..."
	 * @param	boolean	$has_global	first item is "Global - All Stores"
	 * @return	string
	 */
	public function getSelectList($name, $id=0, $has_global=false)
	{
		$rows = $this->getAllRows();
		if(strlen($name)==0)
		{
			$name="store_id";
		}
		$html = "<select class='form-control' id='".$name."' name='".$name."'>";
		if($has_global == true)
		{
			$html .= "<option value='0'>Manually Added</option>";
		}
		else
		{
			if($id==0)
			{
				$html .= "<option value='0' selected>Please Select....</option>";
			}
		}
		foreach($rows as $row)
		{
			if($row->id == $id)
				$html .= "<option value='".$row->id."' selected>".$row->cs_name."</option>";
			else
				$html .= "<option value='".$row->id."'>".$row->cs_name."</option>";
		}
		$html .="</select>";
		return $html;
	}
}
