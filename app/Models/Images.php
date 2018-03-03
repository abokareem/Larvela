<?php
/**
 * \class Images
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2016-07-29
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \breif CRUD layer for the "images" table.
 */
class Images extends Model
{

/**
 * The row ID value from the "images" table.
 *
 * @var int $id
 */
protected $id;


/**
 * The image file name.
 *
 * @var string $image_file_name
 */
protected $image_file_name;


/**
 * The relative path of the image file from the "media" directory.
 *
 * @var string $image_folder_name
 */
protected $image_folder_name;


/**
 * The size of the image file in bytes.
 *
 * @var int $image_size
 */
protected $image_size;


/**
 * The image height.
 *
 * @var int $image_height
 */
protected $image_height;


/**
 * The image width.
 *
 * @var int $image_width
 */
protected $image_width;


/**
 * Image order is used in thumb nails and base images.
 * Smallest thumbnail is 0
 *
 * @var int $image_order
 */
protected $image_order;


/**
 * if this is a thumbnail then the parent ID is the larger base image.
 *
 * @var int $image_parent_id
 */
protected $image_parent_id;


/**
 * Timestamps are not used in the table.
 *
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * Items that are mass assignable.
 *
 * @var array $fillable
 */
protected $fillable = ['image_file_name','image_folder_name','image_size','image_height','image_width','image_order','image_parent_id'];




	/**
	 * Insert a new row into the "images" table.
	 *
	 * @pre Data array must have all required data for insert operation.
	 * @post new Row inserted into "images" table.
	 * @param $d Array of data to insert into "images" table.
	 * @return ID New row ID.
	 */
	public function InsertImage($d)
	{
		return \DB::table('images')->insertGetId( array(
			'image_file_name'=>$d['image_file_name'],
			'image_folder_name'=>$d['image_folder_name'],
			'image_size'=>$d['image_size'],
			'image_height'=>$d['image_height'],
			'image_width'=>$d['image_width'],
			'image_order'=>$d['image_order'],
			'image_parent_id'=>$d['image_parent_id']
			));
	}



	/**
	 * Update a row in the "images" table given an array of data including the row ID column.
	 *
	 * @pre Data array must have all required data for update operation.
	 * @post new Row in "images" table updated.
	 * @param $d Array of data to update the row with.
	 * @return int Number of affected rows updated.
	 */
	public function UpdateImage($d)
	{
		return \DB::table('images')->where(['id'=>$d['id'] ])
			->update( array(
			'image_file_name'=>$d['image_file_name'],
			'image_folder_name'=>$d['image_folder_name'],
			'image_size'=>$d['image_size'],
			'image_height'=>$d['image_height'],
			'image_width'=>$d['image_width'],
			'image_order'=>$d['image_order'],
			'image_parent_id'=>$d['image_parent_id']
			));
	}







	/**
	 * Find and return the smallest thumbnail for this product
	 *
	 * @param	integer	$image_id
	 * @return	object or null
	 */
	public function getThumbNail($image_id)
	{
		$thumbs = $this->getByParentID($image_id);
		foreach($thumbs as $thumb)
		{
			if($thumb->image_order == 1)
			{
				return $thumb;
			}
		}
		return null;
	}



	/**
	 * Return a collection of rows given the parent image row ID.
	 *
	 * @pre ID is a valid integer
	 * @post None
	 * @param $id Integer row ID
	 * @return mixed Collection of row objects.
	 */
	public function getByParentID($id)
	{
		return \DB::table('images')->where(['image_parent_id'=>$id])->get();
	}





	/**
	 * Maps the image to the image_product table using an Eloquent many to many function
	 *
	 * @return 	mixed
	 */
	public function products()
	{
		return $this->belongsToMany('App\Models\Products','image_product','image_id','product_id');
	}

}


