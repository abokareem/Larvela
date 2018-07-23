<?php
/**
 * \class	OrderItem
 * \date	2018-07-17
 * \author	Sid Young <sid@off-grid-engineering.com>
 *
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * \brief Model class for the Order Items table.
 */
class OrderItem extends Model
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
protected $table = "order_items";


/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
public $fillable =['order_item_cid','order_item_oid','order_item_status'];

}
