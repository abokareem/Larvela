<?php
/**
 * \class	StoreController
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-09-15
 *
 *
 * [CC]
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

		$Customer = new Customer;
		$CustomerAddress = new CustomerAddress;
		$CustSource = new CustSource;
	
		$address = new \stdClass;
		$address->customer_cid= 0;
		$address->customer_address = "";
		$address->customer_suburb = "";
		$address->customer_postcode = "";
		$address->customer_city = "";
		$address->customer_state = "";
		$address->customer_country = "AU";

		$user = Auth::user();
		if (Auth::check())
		{
			$source =  $CustSource->getByName("WEBSTORE");
			$store = app('store');
			$customer = Customer::where('customer_email', $user->email)->first();

			$this->LogMsg("Customer Data [".print_r($customer,true)."]");
			if(sizeof($customer)==0)
			{
				$this->LogMsg("No Customer Data! - create a new customer");
				$data = array('customer_name'=>$user->name, 'customer_email'=>$user->email,'customer_mobile'=>'','customer_status'=>'A',
					'customer_source_id'=>$source->id,
					'customer_store_id'=>$store->id
					);
				$Customer->InsertCustomer($data);
				$customer = Customer::where('customer_email', $user->email)->first();
				$this->LogMsg("Customer Data [".print_r($customer,true)."]");
				$data = $CustomerAddress->getArray();
				$data['customer_cid'] = $customer->id;
				$data['customer_email'] = $user->email;
				$this->LogMsg("Insert a blank address");
				$CustomerAddress->InsertAddress($data);
				$address = CustomerAddress::where('customer_cid',$customer->id)->first();
				$this->LogMsg("Fetch Address Data [".print_r($address,true)."]");

			}
			else
			{	
				$this->LogMsg("Fetch pre-existing address data.");
				$address = CustomerAddress::where('customer_cid',$customer->id)->first();
				if(sizeof($address)==0)
				{
					$data = $CustomerAddress->getArray();
					$data['customer_cid'] = $customer->id;
					$data['customer_email'] = $user->email;
					$this->LogMsg("Insert a blank address");
					$CustomerAddress->InsertAddress($data);
					$address = CustomerAddress::where('customer_cid',$customer->id)->first();
				}
			}

			$this->LogMsg("Customer Data [".print_r($customer,true)."]");
			$this->LogMsg("Address Data [".print_r($address,true)."]");
			$this->LogMsg("Store Data [".print_r($store,true)."]");
			$this->LogMsg("User Data [".print_r($user,true)."]");

			$theme_path = \Config::get('THEME_CART')."myaccount";
			return view($theme_path,[
				'user'=>$user,
				'customer'=>$customer,
				'address'=>$address,
				'store'=>$store]);
		}
		return view('auth.login');
	}



	/**
	 *
	 * @return	mixed
	 */
	public function ShowSignUpForm()
	{
		$this->LogFunction("ShowSignUpForm()");
		return view('auth.register');
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
	 * @return	mixed
	 */
	public function ShowAddStorePage()
	{
		$this->LogFunction("ShowAddStorePage()");
		$stores = Store::all();
		return view('Admin.Stores.addstore',['stores'=>$stores]);
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
		return view('Admin.Stores.editstore',['store'=>$store]);
	}




	/**
	 * Process the posted data in the validation class and update back to the DB using the servie layer.
	 *
	 * POST ROUTE: /admin/store/update/{id}
	 *
	 * @return	mixed
	 */
	public function UpdateStore(StoreRequest $request, $id)
	{
		$this->LogFunction("UpdateStore()");
		$this->CreateThemeDir($request->store_env_code);
		$request['id'] = $id;
		StoreService::update($request);
		return $this->ShowStoresPage();
	}




	/**
	 * POST ROUTE: /admin/store/save
	 */
	public function SaveNewStore(StoreRequest $request)
	{
		$this->LogFunction("SaveNewStore()");
		$this->CreateThemeDir($request->store_env_code);
		$this->LogMsg("Save New Store");
		StoreService::insert($request);
		return $this->ShowStoresPage();
	}



	/**
	 * Create Theme Directory given the store code, return false on failure.
	 *
	 * @param	string	$store_code
	 * @return	boolean
	 */
	protected function CreateThemeDir($store_code)
	{
		return true;

		#
		# 2017-08-29 - DISABLED, THEMES NOW IMPLEMENTED
		#
		$paths_to_create = array();
		$path = resource_path('views')."/Themes/".$store_code;
		array_push($paths_to_create, $path);
		$path = resource_path('views')."/Themes/".$store_code."/Monthly";
		array_push($paths_to_create, $path);
		$path = resource_path('views')."/Themes/".$store_code."/Monthly/01";
		array_push($paths_to_create, $path);
		$path = resource_path('views')."/Themes/".$store_code."/Monthly/12";
		array_push($paths_to_create, $path);

		foreach($paths_to_create as $path)
		{
			$this->LogMsg("Path [".$path."]");
			if(!file_exists($path))
			{
				$this->LogMsg("Create Path!");
				try { mkdir($path,0775,true); }
				catch(Exception $e)
				{
					$this->LogError("Failed to create Path [".$finalpath."]");
					return false;
				}
			}
		}
		return true;
	}
}
