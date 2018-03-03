<?php
/**
 * \class	SupportController
 * \date	2017-08-30
 * \author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;


use App\Traits\Logger;


/** 
 * \brief Support page handling logic.
 * The SupportController manages the routing to pre-defined support pages within
 * the Larvela themed environment.
 *
 * @todo Add support for user defined pages.
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
		$this->setFileName("store");
		$this->LogStart();
		$this->LogMsg("CLASS:SupportController");
	}



	/**
	 * Close log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS:SupportController");
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
				return view($blade_path);
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
					return view($blade_path);
				}
			}
		}
		$blade_path = \Config::get('THEME_ERRORS')."no-route";
		$this->LogMsg("Using default blade [".$blade_path."]");
		return view($blade_path);
	}


	/**
	 * invoke the route for the about us page
	 * @return	mixed
	 */ 
	public function about()
	{
		$theme_path = \Config::get('THEME_SUPPORT').'about';
		return view($theme_path);
	}



	/**
	 * invoke the route for the terms and conditions page
	 * @return	mixed
	 */ 
	public function tandc()
	{
		$theme_path = \Config::get('THEME_SUPPORT').'tandc';
		return view($theme_path);
	}



	/**
	 * invoke the route for the support page
	 * @return	mixed
	 */ 
	public function support()
	{
		$theme_path = \Config::get('THEME_SUPPORT').'support';
		return view($theme_path);
	}


	/**
	 * invoke the route
	 * @return	mixed
	 */ 
	public function privacy()
	{
		$theme_path = \Config::get('THEME_SUPPORT').'privacy';
		return view($theme_path);
	}


	/**
	 * invoke the route
	 * @return	mixed
	 */ 
	public function contact()
	{
		$theme_path = \Config::get('THEME_SUPPORT').'contact';
		return view($theme_path);
	}
}
