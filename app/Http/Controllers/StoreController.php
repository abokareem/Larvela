<?php
/**
 * \class	StoreController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-09-15
 * \version	1.0.2
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


use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Input;
use Session;


use App\Http\Requests\StoreRequest;

use App\Services\StoreService;

use App\Models\Store;
use App\Models\StoreSetting;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustSource;

use App\Traits\Logger;
/**
 * \brief MVC Controller for handling basic store functions.
 */
class StoreController extends Controller
{
use Logger;



	/**
	 * Setup Log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("store-admin");
		$this->setClassName("StoreController");
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
	 * Show account details
	 *
	 * GET ROUTE: /myaccount
	 *
	 * @return	mixed
	 */
	public function ShowMyAccount() 
	{
		$this->LogFunction("ShowMyAccount()");
	
		$address = new \stdClass;
		$address->customer_cid= 0;
		$address->customer_address = "";
		$address->customer_suburb = "";
		$address->customer_postcode = "";
		$address->customer_city = "";
		$address->customer_state = "";
		$address->customer_country = "AU";

		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$user = Auth::user();
		if (Auth::check())
		{
			$source = CustSource::where('cs_name',"WEBSTORE")->first();
			$customer = Customer::where('customer_email', $user->email)->first();

	#		$this->LogMsg("Customer Data [".print_r($customer,true)."]");
			if(sizeof($customer)==0)
			{
				$this->LogMsg("No Customer Data! - create a new customer");
				$o = new Customer;
				$o->customer_name = $user->name;
				$o->customer_email = $user->email;
				$o->customer_mobile = '';
				$o->customer_status = "A";
				$o->customer_source_id = $source->id;
				$o->customer_store_id = $store->id;
				$o->save();
				$customer = Customer::where('customer_email', $user->email)->first();
		#		$this->LogMsg("Customer Data [".print_r($customer,true)."]");
				$this->LogMsg("Insert a blank address");
				#
				# {FIX_2018-07-23} - Added customer address using Eloquent.
				#
				$ca = new CustomerAddress;
				$ca->customer_cid = $customer->id;
				$ca->customer_email = $user->email;
				$ca->customer_date_created = date("Y-m-d");
				$ca->customer_date_updated = date("Y-m-d");
				$ca->save();

				$address = CustomerAddress::where('customer_cid',$customer->id)->first();
		#		$this->LogMsg("Fetch Address Data [".print_r($address,true)."]");

			}
			else
			{	
				$this->LogMsg("Fetch pre-existing address data.");
				$address = CustomerAddress::where('customer_cid',$customer->id)->first();
				if(sizeof($address)==0)
				{
					$this->LogMsg("Insert a blank address");
					$ca = new CustomerAddress;
					$ca->customer_cid = $customer->id;
					$ca->customer_email = $user->email;
					$ca->customer_date_created = date("Y-m-d");
					$ca->customer_date_updated = date("Y-m-d");
					$ca->save();
					$address = CustomerAddress::where('customer_cid',$customer->id)->first();
				}
			}

			$this->LogMsg("Customer Data [".print_r($customer,true)."]");
			$this->LogMsg("Address Data [".print_r($address,true)."]");
			$this->LogMsg("Store Data [".print_r($store,true)."]");
			$this->LogMsg("User Data [".print_r($user,true)."]");

			$theme_path = \Config::get('THEME_CART')."myaccount";
			return view($theme_path,[
				'store'=>$store,
				'settings'=>$settings,
				'user'=>$user,
				'customer'=>$customer,
				'address'=>$address
				]);
		}
		return view('auth.login');
	}



	/**
	 *
	 * @return	mixed
	 */
	public function ShowSignUpForm()
	{
		$store = app('store');
		$this->LogFunction("ShowSignUpForm()");
		return view('auth.register',['store'=>$store]);
	}
}




