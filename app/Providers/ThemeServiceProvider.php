<?php
/**
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2017-08-28
 * \class	ThemeServiceProvider
 *
 * [CC]
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
 * to access the required Theme Directory structure. Once "store Theme"
 * is defined, set that as the default theme name.
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
		$theme_name = "default";
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
				$theme_name = $t->theme_name;
				break;
			}
		}

		View::share("THEME_NAME", $theme_name);
		#
		# 3. Build Paths, test and set path, if dir does not exist then set the default path.
		#
		$theme_directories = array("Home","Header","Footer","Support","Includes","Errors","Product","Category","Cart");
		foreach($theme_directories as $theme_dir)
		{
			$theme_u_dir = "THEME_".strtoupper($theme_dir);
			$blade_path  = "Themes.".$theme_name.".".$theme_dir.".";
			$default_blade_path = "Themes.default.".$theme_dir.".";

			$path = resource_path("views/Themes/".$theme_name."/".$theme_dir);
			#echo "PATH: ".$path."<br>";
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
