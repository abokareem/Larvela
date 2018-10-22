<?php
/**
 * \class	AdminController
 * \date	2016-07-01
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version	1.0.3
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
 *
 *
 */
namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Session;
use App\User;
use Redirect;
use App\Models\Order;
use App\Http\Requests;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Jobs\BuildReleaseInfo;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\Services\TemplateService;
use App\Http\Requests\TemplateNewRequest;
use App\Http\Requests\TemplateUpdateRequest;



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
		$this->setFileName("larvela-admin");
		$this->setClassName("AdminController");
		$this->LogStart();
	}


	/**
	 * Close of log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
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
		$User = new User;
		$Customer = new Customer;
		$Order = new Order;

		if(!Gate::allows('admin-only', auth()->user()))
		{
			return Redirect::to('/security/unauthorized');
		}
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
		$new_customers = Customer::whereBetween(
			'customer_date_created',
			array(
				date("Y-m-d", strtotime("-30 days")),
				date("Y-m-d")
				))->get();

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
			$currentuser = User::where('email',Auth::user()->email)->first();
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
}
