<?php
/**
 * \class	AdminController
 * @date	2016-07-01
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use Input;
use Redirect;
use Session;

use App\Models\Customer;
use App\Models\Order;

use App\Models\Users;
use App\Models\Services\TemplateService;
use App\Http\Requests\TemplateNewRequest;
use App\Http\Requests\TemplateUpdateRequest;

use App\Jobs\BuildReleaseInfo;


use App\Traits\Logger;

/**
 * \brief Administration dashboard controller.
 */
class AdminController extends Controller
{
use Logger;

	/**
	 * Construct object and set logging up
	 * 
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("store-admin");
		$this->LogStart();
		$this->LogMsg("CLASS::AdminController");
	}


	/**
	 * Close of log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS::AdminController");
		$this->LogEnd();
	}



	/**
	 * 
	 *
	 *
	 * @todo setup to run from scheduler class
	 */
	public function ReleaseInfo()
	{
		dispatch( new BuildReleaseInfo());
	}





	/**
	 *============================================================
	 *
	 *                   DEVELOPMENT
	 *
	 *============================================================
	 *
	 *
	 * Display the main Administrator dashboard.
	 * Shows orders and subscriptions.
	 * Need to show sales data.
	 *
	 * @todo Gather Sales data for graphing.
	 *
	 * @return  mixed
	 */
	public function ShowDashboard()
	{
		$Users = new Users;
		$Customer = new Customer;
		$Order = new Order;

		#
		# Get all Waiting orders
		# All Dispatched order in the last month
		# All Cancelled orders in the last month
		#
		$last_month = date("Y-m-d", strtotime("-1 month"));
		$today = date("Y-m-d");
		$waiting_orders = Order::where('order_status','W')
			->orWhere('order_status','H')
			->orderBy('order_date')->orderBy('order_time')->get();
		$completed = Order::where('order_status','C')
			->whereBetween('order_date', array($last_month, $today))
			->orderBy('order_date')->orderBy('order_time')->get();
		#
		$dispatched = Order::where('order_status','C')
			->where('order_dispatch_status','D')
			->whereBetween('order_date', array($last_month, $today))
			->orderBy('order_date')->orderBy('order_time')->get();
		$orders = $waiting_orders->merge($completed);
		#
		# this month only option
		#
		$year = date("Y");
		$month = date("m");
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

		#
		# try last 30 days option
		#
		$dates = array();
		for($i = 30; $i >0; $i--) 
		{
		    $dates[$i] = date("Y-m-d", strtotime('-'.$i.' days'));
		}
		#
		# @todo Remove this call into Customer and use Eloquent call
		#
		$new_customers = $Customer->getByDates(date("Y-m-d", strtotime("-30 days")), date("Y-m-d"));

		array_push($dates, date("Y-m-d"));
		if(Auth::check())
		{
			#
			# @todo Extract monthly sales data and populate this array from the orders table.
			#
			$monthlysalesdata = array();

			#
			# date and count of orders for google charts bar chart
			#
			$datarows = array();
			$tabs = "MENU GO HERE";

			$colours = ['#f44336','#e91e63','#9c27b0','#673ab7','#3f51b5','#2196f3','#03a9f4'];
			$colour_index = 0;
			foreach($dates as $d)
			{
				$data = new \stdClass;
				$data->day = $d;
				$data->count_sold = rand(0,100);
				$data->colour = $colours[$colour_index];
				array_push($datarows, $data);
				$colour_index++;
				if($colour_index > 6) $colour_index=0; 
			}
			$currentuser = Users::where('email',Auth::user()->email)->first();
			if($currentuser->id==1)
			{
				return view('Admin.Dashboard.dashboard',[
					'subscriptions'=>$new_customers,
					'orders'=>$orders,
					'orders_waiting'=>$waiting_orders,
					'orders_completed'=>$completed,
					'orders_dispatched'=>$dispatched,
					'datarows'=>$datarows,
					'monthlysalesdata'=>$monthlysalesdata
					]);
			}
		}
		return Redirect::to('/security/unauthorized');
	}




	
	



	/**
	 *============================================================
	 *
	 *                   DEVELOPMENT
	 *
	 *============================================================
	 *
	 *
	 * Invoke a mail run for all customers in the data base
	 * use template in /templates using the suplied name
	 *
	 * @param	string	$template_name
	 * @return	void
	 */
	public function MailRun($template_name="")
	{
		$all_customers = Customer::all();
		foreach($all_customers as $customer)
		{
			print_r($customer);
			echo "</br>";
		}
		#
		# @todo dispatch to job here, need templat or let job get latest template and run
		#
	}



	/**
	 * test method to skip authentication - not for production use
	 *
	 * @return	mixed
	 */
	public function AutoLogIn()
	{
		Auth::loginUsingId(1);
		return Redirect::to('/');
	}


}
