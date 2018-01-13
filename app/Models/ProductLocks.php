<?php
/**
 * \class	ProductLocks
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2017-08-24
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide all CRUD functionality for the "product_locks" table.
 */
class ProductLocks extends Model
{


/**
 * Does not use time stampped columns
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * Columns that are mass-assignable
 * @var array $fillable
 */
protected $fillable = ['product_lock_pid','product_lock_cid','product_lock_qty','product_lock_utime'];





	/**
	 * Return a list of columns
	 *
	 * @return	array
	 */
	public function getFillable()
	{
		return $this->fillable;
	}



	/**
	 *
	 * @param	integer	$id	Row id from the products table.
	 * @return	mixed
	 */
	public function getByID($id)
	{
		return \DB::table('product_locks')->where(['id'=>$id])->first();
	}



	/**
	 *
	 * @param	integer	$id	Row id from the products table.
	 * @return	mixed
	 */
	public function getByCID($id)
	{
		return \DB::table('product_locks')->where(['product_lock_cid'=>$id])->get();
	}



	/** 
	 * Delete a row given the ID.
	 *
	 * @param	integer	$id	Row id from the products table.
	 * @return 	integer	Count of affected rows.
	 */
	public function DeleteByID($id)
	{
		return \DB::table('product_locks')->where(['id'=>$id])->delete();
	}



	/** 
	 * Delete a row given the ID.
	 *
	 * @param	integer	$cart_id
	 * @return 	integer	Count of affected rows.
	 */
	public function DeleteByCID($cart_id)
	{
		return \DB::table('product_locks')->where(['product_lock_cid'=>$id])->delete();
	}



	/** 
	 * given the product id and cart id, insert a new row with the unix time
	 * qty defaults to 1
	 * @return 	integer	Count of affected rows.
	 */
	public function InsertProductLock($cid,$pid,$qty)
	{
		$time = time();
		return \DB::table('product_locks')->insertGetId(array(
			'product_lock_pid'=>$pid,
			'product_lock_cid'=>$cid,
			'product_lock_qty'=>$qty,
			'product_lock_utime'=>$time));
	}




	/**
	 * Update the UNIX timestamp given the specific row ID
	 *
	 * @param	integer	$id
	 * @return	integer
	 */
	public function UpdateTimeStamp($id)
	{
		$time = time();
		return \DB::table('product_locks')->where(['id'=>$id ])->update(['product_lock_utime'=>$time]);
	}


	/**
	 * Update the qty and timestamp given the row ID
	 *
	 * @param	integer	$id		Row ID
	 * @param	integer	$qty
	 * @return	integer
	 */
	public function UpdateQty($id, $qty)
	{
		$time = time();
		return \DB::table('product_locks')->where(['id'=>$id ])
			->update(['product_lock_qty'=>$qty,'product_lock_utime'=>$time]);
	}



	/**
	 * Increment the QTY
	 *
	 * @param	integer	$cid
	 * @param	integer	$pid
	 * @return	integer
	 */
	public function IncrementQty($cid,$pid)
	{
		$time = time();
		$lockrow = \DB::table('product_locks')
			->where(['product_lock_cid'=>$cid,'product_lock_pid'=>$pid])
			->first();
		$qty = $lockrow->product_lock_qty+1;
		return $this->UpdateQty($lockrow->id, $qty);
	}



	/**
	 * Update the UNIX timestamp given the cart ID
	 *
	 * @param	integer	$cart_id
	 * @return	integer
	 */
	public function UpdateByCID($cart_id)
	{
		$time = time();
		return \DB::table('product_locks')->where(['product_lock_cid'=>$cart_id ])->update(['product_lock_utime'=>$time]);
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
}
