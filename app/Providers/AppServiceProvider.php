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

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Stores;

/**
 * \brief AppServiceProvider is an entry point for custom code to load, configure or initialize something during bootup.
 * We try to define a blade variabel and setup our store code.
 * 2017-10-20 - Added check for table existance so when code first deployed the DB table will not exist up till migration has been performed.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		\Blade::extend(function($value) { return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value); });

		if(Schema::hasTable('stores'))
        {
			$Stores = new Stores;
			$store = null;
			if(($store_code=getenv("STORE_CODE"))!=false)
			{
				$store = $Stores->getByCode( $store_code );
			}
			else
			{
				$store = $Stores->getByID(1);
			}
			$this->app->instance('store', $store);
	    }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		foreach(glob(app_path().'/Payments/*_Payment.php') as $filename)
		{
			require_once($filename);
		}

    }
}
