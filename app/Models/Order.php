<?php
/**
 * \class	Order
 * \date	2017-09-18
 * \author	Sid Young <sid@off-grid-engineering.com>
 *
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * \brief Model class for the Orders table.
 */
class Order extends Model
{

/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * The table to use
 * @var string $table
 */
protected $table = "orders";


/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
public $fillable =['order_cart_id','order_cid','order_status','order_payment_status','order_dispatch_status','order_date','order_time'];



	/**
	 * Given a text string containing code, and an object containg row data
	 * return a text string with the codes translated into data and return.
	 *
	 * {FIX_2017-10-26} Order.php - translate() - Added ORDER_NUMBER tag 
	 *
	 * @param   string  $text
	 * @param   mixed   $store
	 * @return  string
	 */
	public function translate($text,$c)
	{
		$number = sprintf("%08d", $c->id);
		$translations = array(
			"{ORDER_ID}"=>$number,
			"{ORDER_NUMBER}"=>$number,
			"{ORDER_REF}"=>$c->order_ref,
			"{ORDER_SRC}"=>$c->order_src,
			"{ORDER_SOURCE}"=>$c->order_src,
			"{ORDER_STATUS}"=>$c->order_status,
			"{ORDER_SHIPPING_VALUE}"=>number_format($c->order_shipping_value,2),
			"{ORDER_VALUE}"=>number_format($c->order_value,2),
			"{ORDER_DATE}"=>$c->order_date_created,
			"{ORDER_TIME}"=>$c->order_date_updated
			);
		return str_replace(array_keys($translations), array_values($translations), $text);
	}

}
