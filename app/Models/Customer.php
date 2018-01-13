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
 * {FIX_2017-10-28} Customer.php - Removed getByID() method
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
	 * Get row(s) given ID
	 *
	 * @param	integer	$id		Row ID to fetch from customers table.
	 * @return	mixed
	 */
	public function getAll()
	{
		return \DB::table('customers')->orderBy('customer_name')->get();
	}



	/**
	 * Get row(s) given ID
	 *
	 * @param	integer	$id		Row ID to fetch from customers table.
	 * @return	mixed
	 */
	public function getActive()
	{
		return \DB::table('customers')->where(['customer_status'=>'A'])->orderBy('customer_date_created')->get();
	}



	/**
	 * Get row(s) given store ID
	 *
	 * @param	integer	$id		Row ID to fetch from customers table.
	 * @return	mixed
	 */
	public function getByStore($id)
	{
		return \DB::table('customers')->where(['customer_store_id'=>$id])->orderBy('customer_date_created')->get();
	}


	public function getByEmail($email)
	{
		return \DB::table('customers')->where(['customer_email'=>$email])->first();
	}

	

	/**
	 * insert a new customer into the DB given an array of column data.
	 *
	 * @param	array	$d
	 * @return	mixed
	 */
	public function InsertCustomer($d)
	{
		$today = date("Y-m-d");
		try
		{
		return \DB::table('customers')->insertGetId( array(
			'customer_name'   =>$d['customer_name'],
			'customer_email'  =>$d['customer_email'],
			'customer_mobile' =>$d['customer_mobile'],
			'customer_status' =>$d['customer_status'],
			'customer_source_id'=>$d['customer_source_id'],
			'customer_store_id' =>$d['customer_store_id'],
			'customer_date_created' =>$today,
			'customer_date_updated' =>$today
			));
		}
		catch(\Illuminate\Database\QueryException $e)
		{
			return 0;
		}
	}



	/**
	 * Update a customer record given an array of column data.
	 *
	 * @param	array	$d
	 * @return	mixed
	 */
	public function UpdateCustomer($d)
	{
		$today = date("Y-m-d");
		return \DB::table('customers')->where(['id'=>$d['id'] ])
				->update( array(
				'customer_name'   =>$d['customer_name'],
				'customer_email'  =>$d['customer_email'],
				'customer_mobile' =>$d['customer_mobile'],
				'customer_status' =>$d['customer_status'],
				'customer_source_id'=>$d['customer_source_id'],
				'customer_store_id' =>$d['customer_store_id'],
				'customer_date_created'=>$d['customer_date_created'],
				'customer_date_updated'=>$today
				));
	}



	/**
	 * Get row(s) given date range
	 *
	 * @param	string	$start_date		Date in string format YYYY-MM-DD
	 * @param	string	$end_date		Date in string format YYYY-MM-DD
	 * @return	mixed
	 */
	public function getByDates($start_date, $end_date)
	{
		return \DB::table('customers')->whereBetween('customer_date_created', array($start_date, $end_date))->get();
	}



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
