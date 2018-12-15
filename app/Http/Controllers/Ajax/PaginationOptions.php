<?php
/**
 * \class	PaginationOptions
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-12-15
 * \version	1.0.0
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
namespace App\Http\Controllers\Ajax;


use Auth;
use Input;
use App\Http\Requests;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Request;

use App\User;
use App\Models\Store;
use App\Models\StoreSetting;
use App\Http\Controllers\Controller;

use App\Traits\Logger;

/**
 * \brief Returns JSON data with the cart items and value
 * In a system with no Administration sub-system, this Controller is required. 
 */
class PaginationOptions extends Controller
{
use Logger;



	/**
	 * Setup logging
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-ajax");
		$this->setClassName("PaginationOptions");
		$this->LogStart();
	}



	/**
	 * Close off log
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}



	/**
	 * AJAX REQUEST -  Count number of items in cart and totalize the cost, return it as JSON data.
	 *
	 * Returns "C" for count of items in cart and "V" for the value of the items (Formatted)
	 *
	 * @return string JSON data with status code
	 */
	public function GetPaginationOptions()
	{
		$this->LogFunction("GetPaginationOptions()");
		$time = time();
		$store = app("store");
		if(Request::ajax())
		{
			$settings = StoreSetting::where('setting_store_id',$store->id)->get();
			$pagination_options = array_fiter($setings->toArray(),function($setting)
			{
				if($setting['setting_name'] == "PAGINATION_OPTIONS") return true;
			});
			if(!is_null($pagination_options))
			{
				$this->LogMsg("Returning user defined pagination options!");
				return json_encode(array("S"=>"OK","O"=>array(array_pop($pagination_options)['setting_value'])));
			}
			$this->LogMsg("Returning default pagination options!");
			return json_encode(array("S"=>"OK","O"=>array(12,24,36,48)));
		}
		else
		{
			$this->LogMsg("Invalid call method!");
			return json_encode(array("S"=>"ERROR", "O"=>0));
		}
	}
}
