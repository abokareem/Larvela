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
	 * Dump the internal state of the object
	 */
	public function dump() { print_r($this->getArray()); }
	


	/**
	 * Returns the array "fillable".
	 *
	 * @return	array	The fillable array.
	 */
	public function getFillable() { return $this->fillable; }



	/**
	 * Return all rows from the database table "image_product".
	 *
	 * @return	mixed	Collection of rows fillable array.
	 */
	public function getAll()
	{
		return \DB::table('image_product')->orderBy('id')->get();
	}



	/**
	 * Given the row ID, access the "image_product" table, return a collection of rows
	 * however the collection will only have a single row (assuming the row ID is valid).
	 *
	 * @pre		Valid row id value needed otherwise empty collection returned.
	 * @param	integer	$id	The row ID we need to retrieve.
	 * @return 	mixed	Collection of row objects but should be a single item.
	 */
	public function getByID($id)
	{
		return \DB::table('image_product')->where(['id'=>$id])->first();
	}



	/**
	 * To be phased out. See getByProductID($id)
	 *
	 * @param	integer	$id The product_id (row ID) from the products table.
	 * @pre		Valid row id value needed otherwise empty collection returned.
	 * @post	None
	 * @return	mixed	Collection of row objects
	 */
	public function getByProduct($id)
	{
		return $this->getByProductID($id);
	}



	/**
	 * Return ALL images associated with a product.
	 *
	 * @param 	integer	$id	The product_id (row ID) from the products table.
	 * @pre		Valid row id value needed otherwise empty collection returned.
	 * @post 	None
	 * @return	mixed	Collection of row objects
	 */
	public function getByProductID($id)
	{
		return \DB::table('image_product')->where(['product_id'=>$id])->get();
	}



	/**
	 * Return ALL images associated with a product.
	 *
	 * @param	integer	$id	The integer row ID of the image from the "images" table.
	 * @pre		None
	 * @post	None
	 * @return	mixed	Collection of row objects
	 */
	public function getByImageID($id)
	{
		return \DB::table('image_product')->where(['image_id'=>$id])->get();
	}



	/**
	 * Return an array of the internal state of this object
	 *
	 * @pre		None
	 * @post	None
	 * @return	array
	 */
	public function getArray()
	{
		return array( 'id'=>$this->id, 'image_id'=>$this->image_id, 'product_id'=>$this->product_id);
	}
	


	/**
	 * Insert a new mapping into the image_product table.
	 *
	 * @param	integer	$product_id	The new product ID
	 * @param	integer	$image_id	The new image ID
	 * @pre		Valid parameters required
	 * @post	Table udpated and internal data also updated.
	 * @return	integer	The inserted row ID
	 */
	public function InsertMapping($product_id, $image_id)
	{
		return \DB::table('image_product')->insertGetId(array(
			'image_id'=>$image_id,
			'product_id'=>$product_id)
			);
	}



	/**
	 * Given the row ID for the image_product table, update the data in a particular row.
	 *
	 * @param	integer	$id	The row ID in the image_product table to change
	 * @param	integer	$product_id The new product ID
	 * @param	integer	$image_id 	The new image ID
	 * @pre		Valid parameters required
	 * @post	Table udpated and internal data also updated.
	 * @return 	integer	Count of affected rows
	 */
	public function UpdateMapping($id, $product_id, $image_id)
	{
		return \DB::table('prod_image_id')
			->where(['id'=>$id])
			->update(array('image_id'=>$image_id, 'product_id'=>$product_id));
	}




	/**
	 * Given the row ID for the image_product table, delete the row
	 *
	 * @param	integer	$id 	The row ID in the image_product table to delete.
	 * @return	integer	Count of affected rows
	 */
	public function DeleteByID($id)
	{
		return \DB::table('image_product')->where(['id'=>$id])->delete();
	}



	/**
	 * Given the image ID in the image_product table, delete the row(s)
	 *
	 * @param	integer	$id	The row ID in the image_product table to delete.
	 * @return	integer	Count of affected rows
	 */
	public function DeleteByImageID($id)
	{
		return \DB::table('image_product')->where(['image_id'=>$id])->delete();
	}



	/**
	 * Given the product ID, remove all applicable rows from the image_product table.
	 *
	 * @param	integer	$id	The product row ID.
	 * @return	integer	Count of affected rows
	 */
	public function DeleteByProductID($id)
	{
		return \DB::table('image_product')->where(['product_id'=>$id])->delete();
	}
	



	/**
	 * Maps a product ID in locks table to products table.
	 *
	 * @return  mixed
	 */
	public function product()
	{
		return $this->belongsTo('App\Models\Products');
	}



	/**
	 * Maps a product ID in locks table to products table.
	 *
	 * @return  mixed
	 */
	public function image()
	{
		return $this->belongsTo('App\Models\Images');
	}


}
