<?php
/**
 * \class	SubscriptionRequest
 * @date	2016-12-15
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Eloquent model for the "store_settings" table.
 */
class SubscriptionRequest extends Model
{


/**
 * @var boolean $timestamps
 */
public $timestamps= false;


/**
 * The items that are mass assignable
 *
 * @var array $fillable
 */
protected $fillable = array('sr_email', 'sr_status', 'sr_process_value', 'sr_date_created', 'sr_date_updated');



	/**
	 * Return a single row given the row ID
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function getByID($id)
	{
		return \DB::table('subscription_request')->where(['id'=>$id])->first();
	}


	/**
	 * Return a single row given the email
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function getByEmail($email)
	{
		return \DB::table('subscription_request')->where(['sr_email'=>$email])->first();
	}



	public function getByStatus($status)
	{
		return \DB::table('subscription_request')->where(['sr_status'=>$status])->get();
	}



	/**
	 * Insert an email and default values for processing subscriptions.
	 *
	 * PROCESS_VALUE
	 * 0 - sent,
	 * 1 - 24hr resend,
	 * 2 > countdown to delete.
	 *
	 * STATUS
	 * W - Waiting for confirmation
	 * C - Confirmed
	 *
	 * @param	string	$email
	 * @return	integer
	 */
	public function InsertSubscription($email)
	{
		
		$today = date("Y-m-d");

		$d = array(
			'sr_email'=>$email,
			'sr_status'=>'W',
			'sr_process_value'=>0,
			'sr_date_created'=>$today,
			'sr_date_updated'=>$today
			);
		try
		{
		return \DB::table('subscription_request')->insertGetId( $d );
		}
		catch(\Illuminate\Database\QueryException $e)
		{
		return 0;
		}
	}


	/**
	 * Raw insert, pass array
	 *
	 * @param	array	$d
	 * @return	integer
	 */
	public function InsertData($d)
	{
		return \DB::table('subscription_request')->insertGetId( $d );
	}





	/**
	 *
	 *
	 *
	 * @param	array	$d
	 * @return	integer
	 */
	public function UpdateSubscription($d)
	{
		return \DB::table('subscription_request')->where(['id'=>$d['id']])->update( $d );
	}



	/**
	 *
	 *
	 *
	 * @param	string	$email
	 * @return	integer
	 */
	public function MarkAsCompleted( $email )
	{
		$today = date("Y-m-d");
		$d = array( 'sr_status'=>'C', 'sr_process_value'=>0, 'sr_date_updated'=>$today);
		return \DB::table('subscription_request')->where(['sr_email'=>$email])->update( $d );
	}



	/**
	 *
	 *
	 *
	 * @param	integer	$id
	 * @return	integer
	 */
	public function DeleteRow($id)
	{
		return \DB::table('subscription_request')->where(['id'=>$id])->delete();
	}




	/**
	 *
	 *
	 *
	 * @param	string	$email
	 * @return	integer
	 */
	public function DeleteSubscription($email)
	{
		return \DB::table('subscription_request')->where(['sr_email'=>$email])->delete();
	}
}
