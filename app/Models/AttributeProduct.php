<?php
/**
 * \class	AttributeProduct
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-01-11
 *
 * [CC]
 *
 * Maps a product with certain attributes when the product is a base/parent product with potentially lots of basic child products.
 *
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
	 * Maps attribute_product table using an Eloquent many to many function
	 *
	 * @return  mixed
	 */
	public function products()
	{
		return $this->belongsToMany('App\Models\Product','attribute_product','attribute_id','product_id');
	}
}
