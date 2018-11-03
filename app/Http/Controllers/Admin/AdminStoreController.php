<?php
/**
 * \class	AdminStoreController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-08-22
 * \version	1.0.4
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
namespace App\Http\Controllers\Admin;


use Auth;
use Input;
use Session;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Services\StoreService;
use App\Http\Requests\StoreRequest;
use App\Http\Controllers\Controller;

use App\Models\Store;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\CustSource;
use App\Models\CustomerAddress;

use App\Traits\Logger;
/**
 * \brief MVC Controller for handling basic store functions.
 */
class AdminStoreController extends Controller
{
use Logger;



	/**
	 * Setup Log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-admin");
		$this->setClassName("AdminStoreController");
		$this->LogStart();
	}
	
	
	/**
	 * Mark end of log file
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}




	/**
	 * Get all stores and return a view listing them
	 *
	 * GET ROUTE: /admin/stores
	 *
	 * @return	mixed
	 */
	public function ShowStoresPage()
	{
		$this->LogFunction("ShowStoresPage()");
		$stores = Store::all();
		return view('Admin.Stores.showstores',['stores'=>$stores]);
	}



	/**
	 * Return a view for adding a new store.
	 *
	 * GET ROUTE: /admin/store/add
	 *
	 * @return	mixed
	 */
	public function ShowAddStorePage()
	{
		$this->LogFunction("ShowAddStorePage()");
		$stores = Store::all();
		$countries = Country::get();
		$currencies = Currency::get();
		$tzdata = explode("/",date_default_timezone_get());

		return view('Admin.Stores.addstore',['stores'=>$stores,'countries'=>$countries,'tzdata'=>$tzdata[0],'currencies'=>$currencies]);
	}




	/**
	 * Return the edit page for editing existing store details.
	 *
	 * GET ROUTE: /admin/store/edit/{id}
	 *
	 * @return	mixed
	 */
	public function ShowEditStorePage($id)
	{
		$this->LogFunction("ShowEditStorePage()");
		$store = Store::find($id);
		$countries = Country::get();
		$currencies = Currency::get();
		return view('Admin.Stores.editstore',['store'=>$store,'countries'=>$countries,'currencies'=>$currencies]);
	}




	/**
	 * Given the store ID, Process the posted data in the validation class and
	 * update the stores table using the service layer.
	 *
	 * POST ROUTE: /admin/store/update/{id}
	 *
	 * @param	app/Http/Request/StoreRequest	$request
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function UpdateStore(StoreRequest $request, $id)
	{

		$this->LogFunction("UpdateStore()");
		$o = Store::find($id);
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
		$o->store_country_code = $request['store_country_code'];
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
		return $this->ShowStoresPage();
	}




	/**
	 *
	 *
	 * POST ROUTE: /admin/store/save
	 *
	 * @param	app/Http/Request/StoreRequest	$request
	 * @return	mixed
	 */
	public function SaveNewStore(StoreRequest $request)
	{
		$this->LogFunction("SaveNewStore()");
		
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
		return $this->ShowStoresPage();
	}
}
