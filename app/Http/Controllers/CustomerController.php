<?php
/**
 * \class	CustomerController
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-05-01
 *
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;


use Auth;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use Input;
use Redirect;
use Session;

use App\Jobs\AutoSubscribeJob;

use	App\Models\Store;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustSource;

use App\Traits\Logger;

/**
 * \brief Handle all aspects of customer admin views and database calls.
 *
 ( {FIX_2017-10-29} CustomerController.php - Removed "Customers" class references
 */
class CustomerController extends Controller
{
use Logger;

	/**
	 * Setup Log file
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->setFileName("store-admin");
		$this->LogStart();
	}
	
	
	
	/**
	 * Mark end of log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}




	/**
	 * Update the user/customers details
	 * User has pressed update on myaccount page
	 *
	 * POST ROUTE: /myaccount/update/{id}
	 *
	 * @param	integer	$id		customer ID
	 * @return	mixed
	 */
	public function UpdateMyAccount($id)
	{
		$Customer = new Customer;
		$CustomerAddress = new CustomerAddress;

		$customer = array();
		$address = array();
		if($id>0)
		{
			$customer = Customer::where('id',$id)->first();
			$address  = CustomerAddress::where('customer_cid', $id)->first();
		}
		$form = Input::all();
		if($form['cid']==$id)
		{
			$customer_array = json_decode(json_encode($customer),true);
			$customer_array['customer_mobile'] = preg_replace('/\D/', '', $form['customer_mobile']);
			$address_array = json_decode(json_encode($address),true);
			$address_array['customer_cid'] = $id;

			$address_array['customer_email'] = strtolower($customer_array['customer_email']);
			$address_array['customer_address'] = ucwords($form['customer_address']);
			$address_array['customer_suburb'] = ucwords($form['customer_suburb']);
			$address_array['customer_postcode'] = preg_replace('/\D/', '', $form['customer_postcode']);
			$address_array['customer_city'] = ucwords($form['customer_city']);
			$address_array['customer_state'] = ucwords($form['customer_state']);
			$address_array['customer_country'] = $form['customer_country'];
			$Customer->UpdateCustomer($customer_array);
			if(sizeof($address) == 0)
			{
				$CustomerAddress->InsertAddress($address_array);
			}
			else
			{
				$address_array['id'] = $address->id;
				$CustomerAddress->UpdateAddress($address_array);
			}
		}
		return Redirect::to("/");
	}


	/**
	 * 
	 *
	 * POST ROUTE: /admin/customer/update/{id}
	 *
	 * @pre form data must be valid user must be logged in, ID must be a valid row id
	 * @post DB table 'customers' updated based on supplied row ID
	 * @return mixed view object
	 */
	public function UpdateCustomer(CustomerRequest $request, $id)
	{
		if(is_numeric($id))
		{
			$Customer = new Customer;

			$row = Customer::where('id',$id)->first();

			$form = Input::all();
			$form['customer_date_created'] = $row->customer_date_created;
			$cid = $Customer->UpdateCustomer($form);
			if($cid>0)
			{
				\Session::flash('flash_message','Customer Updated!');
			}
			else
			{
				\Session::flash('flash_error','No Update to Customer required!');
			}
			return $this->ShowCustomers();
		}
	}




	/**
	 *======================================================================
	 *
	 *                         TODO
	 *
	 *======================================================================
	 * @return	string
	 */
	public function AjaxUpdate()
	{
		$data = array("S"=>"ERROR");
		if(Request::ajax())
		{
			$data_in = Input::all();
			#
			# @todo Add code to do an update via AJAX of the customer data. Do in 2018
			#
			$data = array("S"=>"OK");
		}
		return json_encode($data);
	}


	/**
	 * Return a page that allows us to edit a customer.
	 *
	 * GET ROUTE: /admin/customer/edit/{id}
	 *
	 * {FIX_2017-10-29} ShowEditCustomerPage() - Refactored classes.
	 *
	 * @pre user must be logged in, ID must be a valid row id
	 * @post page rendered
	 * @return mixed view object
	 */
	public function ShowEditCustomerPage($id)
	{
		if(is_numeric($id))
		{
			$Store = new Store;
			$CustSource = new CustSource;

			$store = app('store');
			$customer = Customer::where('id', $id)->first();
			$stores   = $Store->getSelectList("customer_store_id",$customer->customer_store_id, true);
			$sources  = $CustSource->getSelectList("customer_source_id",$customer->customer_source_id,true);
			return view('Admin.Customers.editcustomer',[
				'store'=>$store,
				'customer'=>$customer,
				'store_select_list'=>$stores,
				'source_select_list'=>$sources]);
		}
	}





	/**
	 * Return a page that allows us to add a new customer.
	 *
	 * GET ROUTE: /admin/customer/addnew
	 *
	 * @pre user must be logged in
	 * @post page rendered
	 * @return mixed view object
	 */
	public function ShowAddCustomerPage()
	{
		$Store = new Store;
		$stores = $Store->getSelectList("customer_store_id", 0, true);

		$CustSource = new CustSource;
		$sources = $CustSource->getSelectList("customer_source_id", 0, true);

		return view('Admin.Customers.addcustomer',[
			'store_select_list'=>$stores,
			'source_select_list'=>$sources
			]);
	}



	/**
	 * Admin save customer form  - OLD METHOD
	 *
	 * @todo Need to build a suitable CustomerRequest Class for new customers.
	 *
	 * POST ROUTE: /admin/customer/save
	 *
	 * @pre user must be logged in
	 * @post page rendered
	 * @return mixed view object
	 */
	public function SaveNewCustomer(CustomerRequest $request)
	{
		$store = app('store');
		$user = Auth::user();
		if (Auth::check())
		{
			$Customer = new Customer;

			$form = Input::all();
			$data = array();
			$fields = $Customer->getFillable();
			foreach($form as $k=>$v) array_push($data,$k);
			array_push($fields,'id');
			array_push($fields,'_token');
			array_push($fields,'customer_date_created');
			array_push($fields,'customer_date_updated');
			$missing = array_diff($data,$fields);
			if(sizeof($missing)==0)
			{
				$cid = $Customer->InsertCustomer($form);
				if($cid>0)
				{
					$cmd = new AutoSubscribeJob($store,$form['customer_email']);
					dispatch($cmd);
					\Session::flash('flash_message','Customer inserted!');
				}
				else
				{
					\Session::flash('flash_error','Customer already in Database!');
				}
			}
			else
			{
				\Session::flash('flash_error','Customer fields missing!');
			}
			return $this->ShowCustomers();
		}
		else
		{
			return Redirect::to("/");
		}
	}



	/**
	 *
	 * GET ROUTE: ?
	 *
	 * @return	mixed
	 */
	public function ShowCustomers()
	{
		$user = Auth::user();
		if (Auth::check())
		{
			$Store = new Store;
			$CustSource = new CustSource();

			$stores = $Store->getArray();
			$sources = $CustSource->getArray();
			$customers = \DB::table('customers')->paginate(20);
			return view('Admin.Customers.showcustomers',['customers'=>$customers,'source'=>$sources,'store'=>$stores]);
		}
		else
		{
			return Redirect::to("/");
		}
	}

}
