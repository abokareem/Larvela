<?php
/**
 * \class	StoreInsertTrait
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2019-01-15
 * \version	1.00
 *
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */
namespace App\Traits;

use Session;
use App\Models\Store;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;


/**
 * \brief Store Insert code removed from a AdminStoreController and put into a Trait to see if it was a good idea :)
 */
trait StoreInsertTrait
{


	/**
	 *
	 *
	 *
	 *
	 * @param	app/Http/Request/StoreRequest	$request
	 * @return	mixed
	 */
	public function StoreInsert(StoreRequest $request)
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
}
