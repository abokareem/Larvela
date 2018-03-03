<?php
/**
 * \class	CartItem
 * @date	2016-0905
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 *
 * [CC]
 */
namespace App\Models;
  

use Illuminate\Database\Eloquent\Model;



/**
 * \brief Cart item maps cart and product item
 */
class CartItem extends Model
{

	/**
	 * Map CartItem to Cart
	 *
	 * @return	mixed
	 */
	public function cart()
	{
		return $this->belongsTo('App\Models\Cart');
	}


	/**
	 * Maps a cart item product to products
	 *
	 * @return	mixed
	 */
	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}
}
