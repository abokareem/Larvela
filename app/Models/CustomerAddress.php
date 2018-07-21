<?php
/**
 * \class	CustomerAddress
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-10-29
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide support for the "customer_addresses" table.
 *
 * @todo Not yet used or implemented in any controllers as of 2016-09-05
 */
class CustomerAddress extends Model
{


/**
 * Does nto use timestamped columns
 * @var boolean $timestamps
 */
public $timestamps = false;

/**
 * Columns that are mass-assignable
 * @var array $fillable
 */
protected $fillable = ['customer_cid','customer_email',
		'customer_address','customer_suburb','customer_postcode','customer_city','customer_state','customer_country',
		'customer_date_created','customer_date_updated'];

/**
 * The table name
 * @var string $table
 */
protected $table = "customer_addresses";


	
	/**
	 *
	 *
	 * @return	mixed
	 */
	public function getAll()
	{
		return \DB::table('customer_addresses')->orderBy('id')->get();
	}



	/**
	 *
	 *
	 * @return	mixed
	 */
	public function getArray()
	{
		$data = array();
		foreach($this->fillable as $column)
		{
			$data[$column]="";
		}
		return $data;
	}
	

	/**
	 *
	 *
	 * @return	integer
	 */
	public function InsertAddress($d)
	{
		$today = date("Y-m-d");
		return \DB::table('customer_addresses')->insertGetId( array(
			'customer_cid'     =>$d['customer_cid'],
			'customer_email'   =>$d['customer_email'],
			'customer_address' =>$d['customer_address'],
			'customer_suburb'  =>$d['customer_suburb'],
			'customer_state'   =>$d['customer_state'],
			'customer_postcode'=>$d['customer_postcode'],
			'customer_country' =>$d['customer_country'],
			'customer_date_created'=>$today,
			'customer_date_updated'=>$today
			));
	}



	/**
	 *
	 *
	 * @return	integer
	 */
	public function UpdateAddress($d)
	{
		return \DB::table('customer_addresses')->where(['id'=>$d['id']])
			->update( array(
			'customer_cid'    => $d['customer_cid'],
			'customer_email'  => $d['customer_email'],
			'customer_address'=> $d['customer_address'],
			'customer_suburb' => $d['customer_suburb'],
			'customer_postcode'=>$d['customer_postcode'],
			'customer_city'   => $d['customer_city'],
			'customer_state'  => $d['customer_state'],
			'customer_country' =>$d['customer_country'],
			'customer_date_created'=>$d['customer_date_created'],
			'customer_date_updated'=>$d['customer_date_updated']
			));
	}
}
