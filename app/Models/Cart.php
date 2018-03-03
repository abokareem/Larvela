<?php
/**
 * \class	Cart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-09-01
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Maps cart to user and records access times.
 * user_id, created_at, updated_at
 */
class Cart extends Model
{

	/**
	 * Maps cart to users table
	 *
	 * @return	mixed
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}




	/**
	 * Maps cart to Cart Items
	 *
	 * @return	mixed
	 */
	public function cartItems()
	{
		return $this->hasMany('App\Models\CartItem');
	}

}
