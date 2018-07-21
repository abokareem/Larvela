<?php
/**
 * \class	ProductLock
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
class ProductLock extends Model
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
	 * Maps a product ID in locks table to products table.
	 *
	 * @return  mixed
	 */
	public function product()
	{
		return $this->belongsTo('App\Models\Products');
	}
}
