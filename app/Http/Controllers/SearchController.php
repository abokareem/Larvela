<?php
/**
 * @class	SearchController
 * @date	2018-04-04
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 *
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use Input;

use App\Models\Customer;
use App\Models\Product;



/**
 * \brief handle the selection of and dispatching of mailrun jobs.
 */
class SearchController extends Controller
{


	/**
	 * Return a view of the search output
	 *
	 * GET ROUTE: /admin/search
	 *
	 * @return	mixed
	 */
	public function Search()
	{
		$store = app('store');
		$form = \Input::all();

		$search = "%".$form['search']."%";
		$customers = Customer::where('customer_email','like',$search)
			->orWhere('customer_name','like',$search)
			->get();
		return view("Admin.Search.display",['store'=>$store,'customers'=>$customers]);
	}
}
