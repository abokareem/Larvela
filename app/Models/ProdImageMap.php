<?php
/**
 * \class	ProdImageMaps
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 * This has been replaced with Eloquent Relationship in Products Model Class
 *
 *
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide CRUD layer access to the "image_product" table.
 *
 * - Maps "base images" to products.
 * - Does not map thumbnails, these are still located in the "images" table but referenced using the image_parent_id field.
 */
class ProdImageMap extends Model
{

protected $table ="image_product";

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


}
