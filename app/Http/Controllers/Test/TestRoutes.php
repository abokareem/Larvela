<?php
/**
 * \class	TestRoutes
 * \date	2018-12-16
 * \version	1.0.1
 *
 *
 * \addtogroup Testing
 * TestRoutes - Provides various error routes.
 */
namespace App\Http\Controllers\Test;


use Auth;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;


use App\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Store;
use App\Models\Image;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Models\CategoryImage;




class TestRoutes extends Controller
{


/*============================================================
 *
 *
 *                          THEME_ERRORS
 *
 * cart-item-out-of-stock.blade.php
 * cart-timeout.blade.php
 * no-matching-products.blade.php
 * no-route.blade.php
 * SubscriptionError.blade.php
 *
 *============================================================
 */


	protected function test_show_blade($blade)
	{
		$store=app('store');
		$theme_path = \Config::get('THEME_ERRORS').$blade;
		return view($theme_path)->with('store',$store);
	}

	/**
	 *
	 *
	 * @param	integer	$days
	 * @return	mixed
	 */
	public function test_error_noroute()
	{
		return $this->test_show_blade("no-route");
	}


	public function test_error_subscriptionerror()
	{
		return $this->test_show_blade("SubscriptionError");
	}



	public function test_error_nomatchingproducts()
	{
		return $this->test_show_blade("no-matching-products");
	}

	public function test_error_carttimeout()
	{
		return $this->test_show_blade("cart-timeout");
	}


	public function test_error_cartitemoutofstock()
	{
		$store=app('store');
		$user = Auth::user();
		$products = Product::limit(5)->get();
		$theme_path = \Config::get('THEME_ERRORS')."cart-item-out-of-stock";
		return view($theme_path)->with('store',$store)->with('products',$products)->with('user',$user);
	}
}
