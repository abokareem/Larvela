<?php
/**
 * \class AdvertService
 * @date 2016-12-08
 * @author Sid Young <sid@off-grid-engineering.com>
 */
namespace App\Services;


use App\Http\Requests\AdvertRequest;

use App\Models\Advert;

/**
 * \brief Service layer for the Adverts table
 */
class AdvertService
{

	/**
	 * Insert a new row
	 *
	 * @return	integer
	 */
	public static function insert(AdvertRequest $request)
	{
		$rv = 0;
		$o = new Advert;
		$o->advert_name = $request['advert_name'];
		$o->advert_html_code = $request['advert_html_code'];
		$o->advert_status = $request['advert_status'];
		$o->advert_date_from = $request['advert_date_from'];
		$o->advert_date_to = $request['advert_date_to'];
		$o->advert_store_id = $request['advert_store_id'];
		if(($rv=$o->save()) > 0)
		{
			\Session::flash('flash_message','Advert Saved!');
		}
		else
		{
			\Session::flash('flash_error','Not Saved!');
		}
		return $rv;
	}



	/**
	 * Update an existing row
	 *
	 * @return	integer
	 */
	public static function update(AdvertRequest $request)
	{
		$rv = 0;
		$o = Advert::find($request['id']);
		$o->advert_name = $request['advert_name'];
		$o->advert_html_code = $request['advert_html_code'];
		$o->advert_status = $request['advert_status'];
		$o->advert_date_from = $request['advert_date_from'];
		$o->advert_date_to = $request['advert_date_to'];
		$o->advert_store_id = $request['advert_store_id'];
		if(($rv=$o->save()) > 0)
		{
			\Session::flash('flash_message','Advert Updated!');
		}
		else
		{
			\Session::flash('flash_error','Update failed!');
		}
		return $rv;
	}
}
