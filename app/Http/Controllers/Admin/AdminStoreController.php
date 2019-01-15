<?php
/**
 * \class	AdminStoreController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-08-22
 * \version	1.0.5
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
use App\Traits\StoreInsertTrait;
use App\Traits\StoreUpdateTrait;
/**
 * \brief MVC Controller for handling basic store functions.
 */
class AdminStoreController extends Controller
{
use Logger;
use StoreUpdateTrait;
use StoreInsertTrait;



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
		$store = app('store');
		$stores = Store::all();
		return view('Admin.Stores.showstores',['stores'=>$stores,'store'=>$store]);
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
		$store = app('store');
		$stores = Store::all();
		$countries = Country::get();
		$currencies = Currency::get();
		$tzdata = explode("/",date_default_timezone_get());

		return view('Admin.Stores.addstore',['store'=>$store,'stores'=>$stores,'countries'=>$countries,'tzdata'=>$tzdata[0],'currencies'=>$currencies]);
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
		$this->UpdateStoreTrait($request, $id);
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
		$this->InsertStoreTrait($request);	
		return $this->ShowStoresPage();
	}
}
