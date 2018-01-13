<?php
/**
 * \class	OrderItems
 * \date	2017-09-18
 * \author	Sid Young <sid@off-grid-engineering.com>
 *
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * \brief Model class for the Order Items table.
 */
class OrderItems extends Model
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



	/**
	 * Given a text string containing code, and an object containg row data
	 * return a text string with the codes translated into data and return.
	 *
	 * {INFO_2017-10-26} Added translate() method to OrderItems Model
	 *
	 * @param   string  $text
	 * @param   mixed   $order
	 * @return  string
	 */
	public function translate($text,$c)
	{
		$items = \DB::table('order_items')->where(['order_item_oid'=>$c->id])->orderBy('id')->get();
		$count = sizeof($items);
		$table="<table><thead><th>SKU</th><th>Description</th><th>Qty Ordered</th><th>Item Price</th></thead><tbody>";
		foreach($items as $i)
		{
			$table .= "<tr>";
			$table .= "<td>".$i->order_item_sku."</td>";
			$table .= "<td>".$i->order_item_desc."</td>";
			$table .= "<td>".$i->order_item_qty_purchased."</td>";
			$table .= "<td>".$i->order_item_price."</td>";
			$table .= "</tr>";
		}
		$table .="</tbody></table>";
		$translations = array(
			"{ORDER_ITEMS}"=>$table,
			"{ORDER_ITEM_COUNT}"=>$count
			);
		return str_replace(array_keys($translations), array_values($translations), $text);
	}

}
