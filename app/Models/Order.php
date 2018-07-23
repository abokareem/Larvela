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


}
