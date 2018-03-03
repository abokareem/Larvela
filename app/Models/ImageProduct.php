<?php
/**
 * \class	ImageProduct
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide CRUD layer access to the "image_product" pivot table,
 *
 * - Maps "base images" to products.
 * - Does not map thumbnails, these are still located in the "images" table but referenced using the image_parent_id field.
 */
class ImageProduct extends Model
{

/**
 * Pivot table mapping images and products
 * @var string $table
 */
protected $table = "image_product";

/**
 * Indicates if the model should be timestamped.
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['image_id','product_id'];






	/**
	 * Maps a product ID in locks table to products table.
	 *
	 * @return  mixed
	 */
	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}



	/**
	 * Maps a product ID to products table.
	 *
	 * @return  mixed
	 */
	public function image()
	{
		return $this->belongsTo('App\Models\Image');
	}


}
