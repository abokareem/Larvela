<?php
/**
 * \class	Installer
 * \date	2018-08-14
 * \author	Sid Young <sid@off-grid-engineering.com>
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
namespace App\Http\Controllers;


use Input;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdminDetailsRequest;


use App\User;
use App\Models\Store;
use App\Models\Currency;
use App\Models\Country;
use App\Models\Customer;




/**
 * \brief Larvela installed Business Logic, handles processing the returned data  from the installer views.
 */
class Installer extends Controller
{

	/**
	 * Process the  configuration data to get the ENV data for the APP_KEY
	 * Works in 5.5 using $_ENV['APP_KEY'] but not in later versions???
	 *
	 * @return array
	 */
	public function getAppKey()
	{
		return $_ENV['APP_KEY'];
	}


	/**
	 * Process the Initial Form Details
	 * and save the admin user
	 *
	 * GET ROUTE: /install/save/1
	 *
	 * app_key, admin_name, admin_email, admin_pwd
	 *
	 * @return	mixed
	 */
	public function SaveAdminDetails(AdminDetailsRequest $request)
	{
		$form = \Input::all();
		$countries = Country::get();
		$parts = explode(":", $this->getAppKey());
		$app_key = substr($parts[1],0,8);
		if($form['app_key'] == $app_key)
		{
			$hash = hash('SHA512',$app_key);
			$o = new User;
			$o->name = $form['admin_name'];
			$o->email = $form['admin_email'];
			$o->password = bcrypt($form['admin_pwd']);
			$o->role_id = 1;
			$o->created_at = date("Y-m-d H:i:s");
			$o->updated_at = date("Y-m-d H:i:s");
			$o->save();
			$tzdata = explode("/",date_default_timezone_get());
			return view("Install.install-2",['key_hash'=>$hash,'admin_user'=>$o,'countries'=>$countries,'tzdata'=>$tzdata[0]]);
		}
		return view("Install.install-1");
	}


	/**
	 * Process the Store Details
	 *
	 * GET ROUTE: /install/save/2
	 *
	 * key_hash, store_name, store_env_code and store_country
	 *
	 * @return	mixed
	 */
	public function SaveStoreBasic()
	{
		$form = \Input::all();
		$parts = explode(":", $this->getAppKey());
		$app_key = substr($parts[1],0,8);
		$hash = hash('SHA512',$app_key);
		if($form['key_hash'] == $hash)
		{
			$user = User::first();
			
			#
			# save Store now.
			#
			$s = new Store;
			$name = ucwords(trim($form['store_name']));
			$env_code = strtoupper(trim($form['store_env_code']));
			$iso_code = $form['store_iso_code'];
			$s->store_name = $name;
			$s->store_env_code = $env_code;
			$s->store_country_code = $iso_code;
			$country = Country::where('iso_code',$iso_code)->first();
			$s->store_country = $country->country_name;
			$s->save();
			#
			#
			#save "Admin" Customer and connect to store...
			#
			$c = new Customer;
			$c->customer_name = $user->name;
			$c->customer_email= $user->email;
			$c->customer_mobile = "";
			$c->customer_status = "A";
			$c->customer_source_id = 1;
			$c->customer_store_id = $s->id;
			$c->customer_date_created = date("Y-m-d");
			$c->save();
			$currency = Currency::get();
			return view("Install.install-3",['key_hash'=>$hash,'store'=>$s,'currency'=>$currency]);
		}
		return view("Install.install-2");
	}




	/**
	 * Process store details
	 *
	 * GET ROUTE: /install/save/3
	 *
	 * key_hash, store_url, store_currency, store_hours
	 *
	 * @return	mixed
	 */
	public function SaveStoreDetails()
	{
		$form = \Input::all();
		$parts = explode(":", $this->getAppKey());
		$app_key = substr($parts[1],0,8);
		$hash = hash('SHA512',$app_key);
		if($form['key_hash'] == $hash)
		{
			$s = Store::find($form['id']);
			$s->store_url = trim($form['store_url']);
			$s->store_hours = trim($form['store_hours']);
			$s->store_currency = trim($form['store_currency']);
			$s->store_sales_email = trim($form['store_sales_email']);
			$s->store_contact = trim($form['store_contact']);
			$s->store_address = trim($form['store_address']);
			$s->store_address2 = trim($form['store_address2']);
			$s->save();
			return view("Install.install-completed");
		}
		return view("Install.install-3");
	}



	/**
	 *
	 * POST ROUTE: /install/prev/1
	 */
	public function ShowAdminPage()
	{
		return view("Install.install-1");
	}



	/**
	 *
	 * POST ROUTE: /install/prev/2
	 */
	public function ShowStorePage()
	{
		$form = \Input::all();
		$parts = explode(":", $this->getAppKey());
		$app_key = substr($parts[1],0,8);
		$hash = hash('SHA512',$app_key);
		if($form['key_hash'] == $hash)
		{
			$s = Store::find($form['id']);
			$currency = Currency::get();
			$countries = Country::get();
			$tzdata = explode("/",date_default_timezone_get());
			return view("Install.install-2",[
				'key_hash'=>$hash,
				'store'=>$s,
				'currency'=>$currency,
				'countries'=>$countries,
				'tzdata'=>$tzdata[0] ]);
		}
		return view("Install.install-1");
	}
}
