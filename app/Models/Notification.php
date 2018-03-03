<?php
/**
 * \class	Notification
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use App\Traits\Logger;

/**
 * \brief MVC Model that provides a CRUD layer access for the "notifications" table.
 */
class Notification extends Model
{
use Logger;


/**
 * Indicates if the model should be timestamped.
 *
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * The attributes that are mass assignable.
 *
 * @var array $fillable
 */
public $fillable =['product_code','email_address','date_created','time_created'];



	/**
	 * Insert a row of data into the notification table.
	 * Pass the data values, not an array as usual
	 *
	 * @param	string	$product_code
	 * @param	string	$email_address
	 * @return	integer	Row id of inserted row.
	 */
	public function InsertNotification($product_code, $email_address)
	{
#		if($this->checkEntry($product_code, $email_address)==0)
#		{
			$today = date("Y-m-d");
			$now = date("H:i:s");
			return \DB::table('notifications')->insertGetId(
				array(
				'product_code'=>$product_code,
				'email_address'=>$email_address,
				'date_created'=>$today,
				'time_created'=>$now)
				);
#		}
#		else
#		{
#			return $this->getByProdEmail($product_code, $email_address);
##		}
	}



	/**
	 * Return an integer count of rows for a particular code and email_address.
	 *
	 * @param	string	$product_code	Product sku code.
	 * @param	string	$email_address 	Customers email address.
	 * @return	integer	Count of rows in notification table that already have that code and email (0 or 1).
	 */
	public function checkEntry($product_code, $email_address)
	{
		return \DB::table('notifications')
			->where(['product_code'=>$product_code])
			->where(['email_address'=>$email_address])
			->count();
	}



	
	

	/**
	 * Given a row ID return a Collection of row objects.
	 *
	 * @param	string	$product_code	Product sku code.
	 * @param	string	$email_address 	Customers email address.
	 * @return	mixed	Collection of rows that match the SKU and email provided.
	 */
	public function getByProdEmail($product_code, $email_address)
	{
		return \DB::table('notifications')
			->where(['product_code'=>$product_code])
			->where(['email_address'=>$email_address])
			->get();
	}


	
	/**
	 * Given the product code, get all emails that need to be notified.
	 *
	 * @param	string	$product_code	Product sku code.
	 * @return	mixed	Collection of rows, but may be empty if no valid matches.
	 */
	public function getByProductCode($product_code)
	{
		return \DB::table('notifications')
			->where(['product_code'=>$product_code])
			->orderBy('date_created', 'DESC')
			->orderBy('time_created', 'DESC')
			->get();
	}


	/**
	 * Given the email address, get all products that the customer needs to be notified about.
	 *
	 * @param	string	$email_address 	Customers email address.
	 * @return	mixed	Collection of rows or empty.
	 */
	public function getByEmailAddress($email_address)
	{
		return \DB::table('notifications')
			->where(['email_address'=>$email_address])
			->orderBy('date_created', 'DESC')
			->orderBy('time_created', 'DESC')
			->get();
	}
}
