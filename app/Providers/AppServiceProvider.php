<?php
/**
 * \class	AppServiceProvider
 * 
 *
 *
 *
 * \addtogroup Internal
 * AppServiceProvider - Modified to load store data into global variable, use $store = app('store'); to retireve.
 * If the environment variable STORE_CODE is not set, it will return the first store in the database table.
 */

namespace App\Providers;
use Config;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Store;

/**
 * \brief AppServiceProvider is an entry point for custom code to load, configure or initialize something during bootup.
 * We try to define a blade variabel and setup our store code.
 * 2017-10-20 - Added check for table existance so when code first deployed the DB table will not exist up till migration has been performed.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * {FIX_2017-10-28} "AppServiceProvider.php" - Refactored.
     * {FIX_2018-03-04} "AppServiceProvider.php" - Refactored find()
     * @return void
     */
    public function boot()
    {
		\Blade::extend(function($value) { return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value); });

		$store = null;
		if(Schema::hasTable('stores'))
        {
			$Store = new Store;
			if(($store_code=getenv("STORE_CODE"))!=false)
			{
				$store = Store::where('store_env_code', $store_code )->first();
			}
			else
			{
				$store = Store::find(1);
			}
			if(is_null($store))
			{
				$store = new Store;
				$store->store_name = "DEMO";
			}
			$this->app->instance('store', $store);
			Config::set("app.url", $store->store_url);
	    }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		foreach(glob(app_path().'/PaymentGateways/*_Payment.php') as $filename)
		{
			require_once($filename);
		}

    }
}
