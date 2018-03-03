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







	public function UpdateProductType($id,$type)
	{
		return \DB::table('product_types')->where(['id'=>$id])->update(['product_type'=>$type]);
	}



}
