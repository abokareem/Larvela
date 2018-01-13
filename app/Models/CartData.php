<?php
/**
 * \class	CartData
 * \date	2017-09-07
 * \author	Sid Young
 *
 *
 *
 * [CC]
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \brief	Cart Data holds additional data for the cart that must transition from /cart to /.shipping to /confirm etc
 */
class CartData extends Model
{
protected $table = "cart_data";
public $fillable = ['cd_cart_id','cd_payment_method','cd_shipping_method'];


	/**
	 * Map CartItem to Cart
	 *
	 * @return  mixed
	 */
	public function cart()
	{
		return $this->belongsTo('App\Models\Cart','cd_cart_id');
	}

}
