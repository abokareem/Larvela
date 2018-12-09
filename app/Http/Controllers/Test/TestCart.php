<?php
/**
 * \class	TestCart
 *
 * \addtogroup Testing
 * TestCart - Provides various manual test routines sending to system_user
 */
namespace App\Http\Controllers\Test;

use Auth;
use Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Jobs\EmptyCartJob;
use App\Jobs\ResizeImages;
use App\Jobs\EmailUserJob;
use App\Jobs\AbandonedCart;
use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\BuildReleaseInfo;
use App\Jobs\CleanupOrphanImages;
use App\Jobs\AbandonedWeekOldCart;
use App\Jobs\ProcessAbandonedCart;

use App\Jobs\LoginFailed;
use App\Mail\LoginFailedEmail;

use App\Jobs\OrderCancelled;
use App\Jobs\OrderCompleted;
use App\Jobs\OrderDispatched;
use App\Jobs\OrderDispatchPending;
use App\Jobs\OrderPaid;
use App\Jobs\OrderPlaced;

use App\Jobs\SendWelcome;
use App\Jobs\BackInStock;
use App\Jobs\OutOfStockJob;
use App\Jobs\FinalSubRequest;
use App\Jobs\UpdateCartLocks;
use App\Jobs\ReSendSubRequest;
use App\Mail\BackInStockEmail;
use App\Jobs\AutoSubscribeJob;
use App\Jobs\PostPurchaseEmail;
use App\Jobs\CheckPendingOrders;
use App\Jobs\ConfirmSubscription;
use App\Jobs\ProcessSubscriptions;
use App\Jobs\SubscriptionConfirmed;
use App\Mail\ConfirmSubscriptionEmail;

use App\Mail\MailOut;
use Illuminate\Support\Facades\Mail;

use App\Mail\AbandonedCartEmail;
use App\Mail\AbandonedWeekOldCartEmail;

use App\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Store;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\Notification;


use App\Events\Larvela\AddToCartMessage;
use App\Events\Larvela\DispatcherFactory;


/**
 * \brief Cart specific manual testing and examination code
 */
class TestCart extends Controller
{



/*============================================================
 *
 *
 *                          CART 
 *
 *
 *============================================================
 */

	public function test_cart_ajax()
	{
		$store = app('store');
		return view("test-ajax",['store'=>$store]);
	}



	public function test_cart_data()
	{
		$v = 0;
		$c = 0;
		if(Auth::check())
		{
			$cart = Cart::where('user_id',Auth::user()->id)->first();
			if($cart)
			{
				echo "cart present";
				$items = $cart->items;
				if(!is_null($items))
				{
					echo "cart has [".sizeof($items)."] items";
					foreach($items as $item)
					{
						$product = Product::find($item->product_id);
						$v += $product->prod_retail_cost;
						$c += $item->qty;
					}
					$data = array('c'=>$c,'v'=>number_format($v,2));
					echo json_encode($data);
				}
				else
				{
					echo "Cart has no items";
				}
			}
		}
		else
		{
			die("You need to put some items in the cart and then call this test");
		}
	}



	/**
	 *
	 *
	 * @param	integer	$days
	 * @return	mixed
	 */
	public function test_cart_abandoned($days=0)
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		$customer = Customer::where('customer_email',$email)->first();
		$cart = Cart::where('user_id',1)->first();
		if($days == 0)
		{
			Mail::to($email)->send(new AbandonedCartEmail($store, $email, $cart));
		}
		else
		{
			Mail::to($email)->send(new AbandonedWeekOldCartEmail($store, $email, $cart));
		}
		$hash="1";
		return view('Mail.RD.cart_abandoned',[
			'store'=>$store,
			'email'=>$email,
			'customer'=>$customer,
			'cart'=>$cart,
			'hash'=>$hash
			]);
	}
}
