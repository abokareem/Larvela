<?php
/**
 * \class	ProductType
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-01-05
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide all CRUD functionality for the "product_types" table.
 */
class ProductType extends Model
{
public $timestamps = false;
protected $fillable = ['product_type'];



	/**
	 *
	 * @return	mixed
	 */
	public function getAll()
	{
		return \DB::table('product_types')->get();
	}



	/**
	 *
	 * @param	integer	$id	Row id from the product_types table.
	 * @return	mixed
	 */
	public function getByID($id)
	{
		return \DB::table('product_types')->where(['id'=>$id])->first();
	}



	/** 
	 * Delete a row given the ID.
	 *
	 * @param	integer	$id	Row id from the product_types table.
	 * @return 	integer	Count of affected rows.
	 */
	public function DeleteByID($id)
	{
		return \DB::table('product_types')->where(['id'=>$id])->delete();
	}



	public function InsertProductType($type)
	{
		return \DB::table('product_types')->insertGetId( array('product_type'=>$type));
	}


	public function UpdateProductType($id,$type)
	{
		return \DB::table('product_types')->where(['id'=>$id])->update(['product_type'=>$type]);
	}



	/**
	 * Return a HTML select list and allow some preset values
	 *
	 * @param	integer	$id			Mark as selected
	 * @param	string	$name		Form control name and ID
	 * @return	string
	 */
	public function getSelectList($form_name="prod_type", $id=1)
	{
		$rows = $this->getAll();
		$html = "<select class='form-control' id='".$form_name."' name='".$form_name."'>";
		foreach($rows as $row)
		{
			if($row->id == $id)
			$html .= "<option value='".$row->id."' selected>".$row->product_type."</option>";
			else
				$html .= "<option value='".$row->id."'>".$row->product_type."</option>";
		}
		$html .="</select>";
		return $html;
	}
}
