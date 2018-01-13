<?php
/**
 * \class StoreService
 */
namespace App\Services;


use App\Http\Requests\StoreRequest;

use App\Models\Store;


/**
 * \brief Service layer for the stores model
 */
class StoreService
{

	public static function getData(StoreRequest $d)
	{
		$r = array(
			'store_env_code'=>$d['store_env_code'],
			'store_name'=>$d['store_name'],
			'store_url'=>$d['store_url'],
			'store_currency'=>$d['store_currency'],
			'store_status'=>$d['store_status'],
			'store_parent_id'=>$d['store_parent_id'],
			'store_logo_filename'=>$d['store_logo_filename'],
			'store_logo_alt_text'=>$d['store_logo_alt_text'],
			'store_logo_thumb'=>$d['store_logo_thumb'],
			'store_logo_invoice'=>$d['store_logo_invoice'],
			'store_logo_email'=>$d['store_logo_email'],
			'store_hours'=>$d['store_hours'],
			'store_sales_email'=>$d['store_sales_email'],
			'store_address'=>$d['store_address'],
			'store_address2'=>$d['store_address2'],
			'store_contact'=>$d['store_contact'],
			'store_bg_image'=>$d['store_bg_image']
			);
	}


	public static function insert(StoreRequest $request)
	{
		$Store = new Store;
		$d = array($request->except('_token') );
		$rv = $Store->InsertStore( $d[0] );
		if($rv > 0)
		{
			\Session::flash('flash_message','Store Saved!');
		}
		return $rv;
	}



	public static function update(StoreRequest $request)
	{
		$Store = new Store;
		$d = array($request->except('_token') );
		$rv = $Store->UpdateStore( $d[0] );
		if($rv > 0)
		{
			\Session::flash('flash_message','Store updated successfully!');
		}
		return $rv;
	}
}
