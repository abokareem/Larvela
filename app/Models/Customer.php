<?php
/**
 * \class	Customer
 * \date	2016-07-29
 * \author	Sid Young <sid@off-grid-engineering.com>
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief  MVC Model to provide support for the "customers" table.
 *
 * Customers is used in conjunction with the subscriptions and users table.
 * users - authenticated users of web site
 * customers - recorded from sales on eBay and via subscriptions, contains extended data.
 * subscriptions - send email offers to these people.
 *
 * {FIX_2017-10-28} Customer.php - Removed obsolete Query Builder methods.
 */
class Customer extends Model
{

/**
 * The table name used for Eloquent queries
 * @var string $table
 */
protected $table = "customers";

/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * Columns that can be changed with mass assignment
 * @var array $fillable
 */
protected $fillable = ['customer_name', 'customer_email','customer_mobile', 'customer_status', 'customer_source_id','customer_store_id', 'customer_date_created','customer_date_updated','customer_address','customer_suburb','customer_postcode','customer_city','customer_state','customer_country'];
	



	/**
	 * Given a text string containing code, and an object containg row data
	 * return a text string with the codes translated into data and return.
	 *
	 * @param   string  $text
	 * @param   mixed   $store
	 * @return  string
	 */
	public function translate($text,$c)
	{
		$parts = explode(" ", $c->customer_name);
		$first = $middle = $last ="";
		switch(sizeof($parts))
		{
			case 1:
				$first = $last = $parts[0];
				break;
			case 2:
				$first = $parts[0];
				$last  = $parts[1];
				break;
			case 3:
				$first = $parts[0];
				$middle= $parts[1];
				$last  = $parts[2];
				break;
			default:
				$first = $parts[0];
				break;
		}
		$translations = array(
			"{CUSTOMER_ID}"=>$c->id,
			"{CUSTOMER_NAME}"=>$c->customer_name,
			"{CUSTOMER_FIRST_NAME}"=>$first,
			"{CUSTOMER_MIDDLE_NAME}"=>$middle,
			"{CUSTOMER_LAST_NAME}"=>$last,
			"{CUSTOMER_EMAIL}"=>$c->customer_email,
			"{CUSTOMER_MOBILE}"=>$c->customer_mobile,
			"{CUSTOMER_DATE_CREATED}"=>$c->customer_date_created,
			"{CUSTOMER_DATE_UPDATED}"=>$c->customer_date_updated
			);
		return str_replace(array_keys($translations), array_values($translations), $text);
	}

}
