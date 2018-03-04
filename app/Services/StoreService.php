<?php
/**
 * \class	StoreService
 * @author	Sid Young	<sid@off-grid-engineering.com>
 * @date	2017-06-30
 *
 *
 */
namespace App\Services;


use App\Http\Requests\StoreRequest;

use App\Models\Store;


/**
 * \brief Service layer for the stores model
 *
 * {FIX_2018-03-04} Converted to pure Eloquent insert/update
 */
class StoreService
{
	public static function insert(StoreRequest $request)
	{
		$o = new Store;
		$o->store_env_code = $request['store_env_code'];
		$o->store_name = $request['store_name'];
		$o->store_url = $request['store_url'];
		$o->store_currency = $request['store_currency'];
		$o->store_status = $request['store_status'];
		$o->store_parent_id = $request['store_parent_id'];
		$o->store_logo_filename = $request['store_logo_filename'];
		$o->store_logo_alt_text = $request['store_logo_alt_text'];
		$o->store_logo_thumb = $request['store_logo_thumb'];
		$o->store_logo_invoice = $request['store_logo_invoice'];
		$o->store_logo_email = $request['store_logo_email'];
		$o->store_hours = $request['store_hours'];
		$o->store_sales_email = $request['store_sales_email'];
		$o->store_address = $request['store_address'];
		$o->store_address2 = $request['store_address2'];
		$o->store_contact = $request['store_contact'];
		$o->store_bg_image = $request['store_bg_image'];
		if($o->save() >0)
		{
			\Session::flash('flash_message','Store Saved!');
			return $o->id;
		}
		else
		{
			\Session::flash('flash_error','ERROR - Store insert failed!');
			return 0;
		}
	}



	public static function update(StoreRequest $request)
	{
		$o = Store::find($request['id']);
		$o->store_env_code = $request['store_env_code'];
		$o->store_name = $request['store_name'];
		$o->store_url = $request['store_url'];
		$o->store_currency = $request['store_currency'];
		$o->store_status = $request['store_status'];
		$o->store_parent_id = $request['store_parent_id'];
		$o->store_logo_filename = $request['store_logo_filename'];
		$o->store_logo_alt_text = $request['store_logo_alt_text'];
		$o->store_logo_thumb = $request['store_logo_thumb'];
		$o->store_logo_invoice = $request['store_logo_invoice'];
		$o->store_logo_email = $request['store_logo_email'];
		$o->store_hours = $request['store_hours'];
		$o->store_sales_email = $request['store_sales_email'];
		$o->store_address = $request['store_address'];
		$o->store_address2 = $request['store_address2'];
		$o->store_contact = $request['store_contact'];
		$o->store_bg_image = $request['store_bg_image'];

		if($o->save() > 0)
		{
			\Session::flash('flash_message','Store updated successfully!');
		}
		else
		{
			\Session::flash('flash_error','ERROR - Store update failed!');
			return 0;
		}
	}
}
