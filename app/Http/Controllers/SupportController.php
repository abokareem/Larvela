<?php
/**
 * \class	SupportController
 * \date	2017-08-30
 * \author	Sid Young <sid@off-grid-engineering.com>
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
 *
 *
 * \addtogroup RequiredControllers
 * SupportController - The SupportController manages the routing to pre-defined support pages within
 * the Larvela themed environment. These are typically "/about" /contact" etc.
 * - If a requested route is not found then we test if it is located in the Support directory.
 * - If found, formulate the path and call the support page.
 * - Use defined pages are not apssed any system variables but can stil laccess the "store" object.
 */
namespace App\Http\Controllers;

use App\Models\StoreSetting;
use App\Traits\Logger;


/** 
 * \brief Support page handling logic.
 * The SupportController manages the routing to pre-defined support pages within
 * the Larvela themed environment.
 *
 * If requested route is not found then test if it is located in the Support directory,
 * formulate path and call.
 */
class SupportController extends Controller
{
use Logger;



	/**
	 * Open log file
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("SupportController");
		$this->LogStart();
	}



	/**
	 * Close log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}




	/**
	 * Catch undefined routes and see if it is a user supplied Support page.
	 * 
	 * {INFO_2017-10-29} SupportController - added method to capture and route undefined page routes. 
	 * 
	 * @param	string	$page_name 
	 * @return	mixed
	 */ 
	public function user_defined_page($page_name)
	{
		$this->LogFunction("user_defined_page");
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$theme_name = \Config::get('THEME_NAME');
		$path = resource_path("views/Themes/".$theme_name."/Support");
		if(file_exists($path) && is_dir($path))
		{
			$this->LogMsg("Support path exists");
			$path = resource_path("views/Themes/".$theme_name."/Support/").$page_name.".blade.php";
			$this->LogMsg("Path to use [".$path."]");
			if(file_exists($path))
			{
				$blade_path = \Config::get('THEME_SUPPORT').$page_name;
				$this->LogMsg("Fetching blade [".$blade_path."]");
				return view($blade_path,['store'=>$store,'settings'=>$settings]);
			}
		}
		else
		{
			$this->LogMsg("Testing for default Support path");
			$path = resource_path("views/Themes/default/Support");
			if(file_exists($path) && is_dir($path))
			{
				$this->LogMsg("default Support path found");
				$path = resource_path("views/Themes/default/Support/").$page_name.".blade.php";
				$this->LogMsg("Path to use [".$path."]");
				if(file_exists($path))
				{
					$blade_path = "Themes.default.Support.".$page_name;
					$this->LogMsg("Fetching blade [".$blade_path."]");
					return view($blade_path,['store'=>$store,'settings'=>$settings]);
				}
			}
		}
		$blade_path = \Config::get('THEME_ERRORS')."no-route";
		$this->LogMsg("Using default blade [".$blade_path."]");
		return view($blade_path,['store'=>$store,'settings'=>$settings]);
	}


	/**
	 * Invoke the route for the about us page - this is a "Standard" route that would be on most sites.
	 * @return	mixed
	 */ 
	public function about()
	{
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$theme_path = \Config::get('THEME_SUPPORT').'about';
		return view($theme_path,['store'=>$store,'settings'=>$settings]);
	}



	/**
	 * invoke the route for the terms and conditions page
	 * @return	mixed
	 */ 
	public function tandc()
	{
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$theme_path = \Config::get('THEME_SUPPORT').'tandc';
		return view($theme_path,['store'=>$store,'settings'=>$settings]);
	}



	/**
	 * invoke the route for the support page
	 * @return	mixed
	 */ 
	public function support()
	{
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$theme_path = \Config::get('THEME_SUPPORT').'support';
		return view($theme_path,['store'=>$store,'settings'=>$settings]);
	}


	/**
	 * invoke the route
	 * @return	mixed
	 */ 
	public function privacy()
	{
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$theme_path = \Config::get('THEME_SUPPORT').'privacy';
		return view($theme_path,['store'=>$store,'settings'=>$settings]);
	}


	/**
	 * invoke the route
	 * @return	mixed
	 */ 
	public function contact()
	{
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$theme_path = \Config::get('THEME_SUPPORT').'contact';
		return view($theme_path,['store'=>$store,'settings'=>$settings]);
	}
}
