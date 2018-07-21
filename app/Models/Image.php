<?php
/**
 * \class	Image
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent model that uses the "images" table.
 *
 * {FIX_2017-10-31} Image.php - Began refactoring the code.
 */
class Image extends Model
{


/**
 * Table name
 * @var string $table
 */
protected $table = "images";




/**
 * Timestamps are not used in the table.
 * @var boolean $timestamps
 */
public $timestamps = false;



/**
 * Items that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['image_file_name','image_folder_name','image_size','image_height','image_width','image_order','image_parent_id'];








	/**
	 * Return a collection of images which are children of this parent.
	 *
	 * @return	mixed
	 */
	public function thumbnails()
	{
		return $this->hasMany('App\Models\Image','image_parent_id');
	}





	/**
	 * Maps the image to the image_product table using an Eloquent many to many function
	 *
	 * @return 	mixed
	 */
	public function products()
	{
		return $this->belongsToMany('App\Models\Product','image_product','image_id','product_id');
	}

}


