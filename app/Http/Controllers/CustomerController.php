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
		$CustomerAddress = new CustomerAddress;

		$customer = array();
		$address = array();
		if($id>0)
		{
			$customer = Customer::find($id);
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

			$customer = Customer::find($id);
			$customer->customer_mobile = $form['customer_mobile'];
			$customer->customer_date_updated = date("Y-m-d");
			$customer->save();
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
			$form = Input::all();
			$o = Customer::find($id);
			$o->customer_name = $form['customer_name'];
			$o->customer_email = $form['customer_email'];
			$o->customer_mobile = $form['customer_mobile'];
			$o->customer_status = $form['customer_status'];
			$o->customer_source_id= $form['customer_source_id'];
			$o->customer_store_id = $form['customer_store_id'];
			$o->customer_date_updated  = date("Y-m-d");
			if($o->save() >0)
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

			$store = app('store');
			$customer = Customer::find($id);
			$stores   = Store::all();
			$sources  = CustSource::all();
			return view('Admin.Customers.editcustomer',[
				'store'=>$store,
				'customer'=>$customer,
				'stores'=>$stores,
				'customer_sources'=>$sources]);
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
		$store = app('store');
		$stores = Store::all();
		$sources = CustSource::all();

		return view('Admin.Customers.addcustomer',[
			'store'=>$store,
			'stores'=>$stores,
			'sources'=>$sources
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
			$form = Input::all();
			$o = new Customer();
			$o->customer_name = $form['customer_name'];
			$o->customer_email = $form['customer_email'];
			$o->customer_mobile = $form['customer_mobile'];
			$o->customer_status = $form['customer_status'];
			$o->customer_source_id= $form['customer_source_id'];
			$o->customer_store_id = $form['customer_store_id'];
			$o->customer_date_created = date("Y-m-d");
			$o->customer_date_updated = date("Y-m-d");
			if($o->save() >0)
			{
				$cmd = new AutoSubscribeJob($store,$form['customer_email']);
				dispatch($cmd);
				\Session::flash('flash_message','Customer inserted!');
			}
			else
			{
				\Session::flash('flash_error','Customer already in Database!');
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
	 * GET ROUTE: /admin/customers
	 *
	 * @return	mixed
	 */
	public function ShowCustomers()
	{
		$user = Auth::user();
		if (Auth::check())
		{
			$store = app('store');
			$stores = Store::all();
			$sources = CustSource::all();
			$customers = \DB::table('customers')->paginate(20);
			return view('Admin.Customers.showcustomers',[
				'customers'=>$customers,
				'sources'=>$sources,
				'stores'=>$stores
				]);
		}
		else
		{
			return Redirect::to("/");
		}
	}

}
