<?php
/**
 * \class	ThemeServiceProvider
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-08-28
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
 * \addtogroup Themes
 * ThemeServiceProvider - Loads theme variables for use in Controllers and Views.
 */
namespace App\Providers;


use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Theme;



/**
 * \brief Provides a series of global variables that can be used
 * to access the required Theme Directory structure.
 * Once the "store Theme" is defined, set that as the default theme name.
 * 2017-10-20 - Added support for DB table check as it will not during initial install.
 */
class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Assign our theme paths, point to default themem path if named theme does not pave the required path.
     * That way you can implement but some components as needed (like a different header at certain times of the year).
	 *
	 * @todo Add code to support a "store theme", defined in "store" table.
	 *
	 *
     * @return void
     */
    public function boot()
    {
		$temp_theme_name = "";
		$theme_name = "default";
		#
		# {INFO_2018-10-10} ThemeServiceProvider - Add support for store theme.
		#
		if(Schema::hasTable('stores'))
		{
			$store = app('store');
			if(strlen($store->store_theme)>3)
			{
				$theme_name = $store->store_theme;
			}
		}

		$today = strtotime(date("Y-m-d"));
		#
		# 1. Get theme names from DB table
		#
		$theme_data = array();
		if(Schema::hasTable('themes'))
		{
			$theme_data = Theme::all();
		}
		foreach($theme_data as $t)
		{
			#
			# 2. Test for the required date range.
			#
			if(($today >= strtotime($t->theme_date_from)) && ($today <=strtotime($t->theme_date_to)))
			{
				$temp_theme_name = $t->theme_name;
				break;
			}
		}

		View::share("THEME_NAME", $theme_name);
		#
		# 3. Build Paths, test and set path, if dir does not exist then set the default path.
		#
		$theme_directories = array("Home","Header","Footer","Support","Includes","Errors","Product","Category","Cart","Auth");
		foreach($theme_directories as $theme_dir)
		{
			$theme_u_dir = "THEME_".strtoupper($theme_dir);
			$blade_path  = "Themes.".$theme_name.".".$theme_dir.".";
			$default_blade_path = "Themes.default.".$theme_dir.".";

			#
			# 
			#
			$path = resource_path("views/Themes/".$theme_name."/".$theme_dir);
			if(file_exists($path) && is_dir($path))
			{
				View::share($theme_u_dir, $blade_path);
				\Config::set($theme_u_dir , $blade_path);
			}
			else
			{
				View::share($theme_u_dir, $default_blade_path);
				\Config::set($theme_u_dir, $default_blade_path);
			}
		}
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
