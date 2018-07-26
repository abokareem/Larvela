<?php
/**
 * \class	TestController
 *
 * \addtogroup Testing
 * TestController - Provides various manual test routines sending to system_user
 * - 2018-07-17 Decided to standardise the method name and route to test_x_y()
 * - Need to make constructor calls uniform (store, email, object)
 * - Need to convert to Mailable interfaces when sending emails.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;

use App\Jobs\QueueTest;
use App\Jobs\MailRun;

use App\Jobs\AbandonedCart;
use App\Jobs\AbandonedWeekOldCart;
use App\Jobs\ProcessAbandonedCart;


use App\Jobs\BuildReleaseInfo;

use App\Jobs\CleanupOrphanImages;
use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\ResizeImages;
use App\Jobs\EmailUserJob;

use App\Jobs\EmptyCartJob;

use App\Jobs\LoginFailedEmail;

use App\Jobs\OrderCancelled;
use App\Jobs\OrderCompleted;
use App\Jobs\OrderDispatched;
use App\Jobs\OrderDispatchPending;
use App\Jobs\OrderPaid;
use App\Jobs\OrderPlaced;
use App\Jobs\CheckPendingOrders;

use App\Jobs\OutOfStockJob;
use App\Jobs\BackInStock;

use App\Jobs\PostPurchaseEmail;

use App\Jobs\ProcessSubscriptions;
use App\Jobs\ConfirmSubscription;
use App\Jobs\SubscriptionConfirmed;
use App\Jobs\ReSendSubRequest;
use App\Jobs\FinalSubRequest;
use App\Jobs\SendWelcome;
use App\Jobs\AutoSubscribeJob;


use App\Jobs\UpdateCartLocks;

use Illuminate\Support\Facades\Mail;
use App\Mail\MailOut;

use App\Mail\OrderPendingEmail;
use App\Mail\OrderPlacedEmail;
use App\Mail\OrderPaidEmail;
use App\Mail\OrderUnPaidEmail;
use App\Mail\OrderOnHoldEmail;
use App\Mail\OrderDispatchedEmail;
use App\Mail\OrderCancelledEmail;

use App\Mail\AbandonedCartEmail;
use App\Mail\AbandonedWeekOldCartEmail;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;


class TestController extends Controller
{





	// test/fs
	public function fs()
	{
		$store=app('store');
		dispatch(new FinalSubRequest($store, $email));
	}



	/**
	 * GET ROUTE: /test/url
	 *
	 * @return	mixed
	 */
	public function test_url()
	{
		Config::set("app.url", "https://buggsbunny.com");
		$url = Config::get("app.url");
		echo "<h1>URL [".$url."] <h1>";
		$store=app('store');
		dd($store);
	}



	public function test_footer()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		$customer = Customer::where('customer_email',$email)->first();
		$order = Order::find(28);
		$order_items = OrderItem::where('order_item_oid',$order->id)->get();
		$hash="1234";
		return view('Mail.RD.order_paid',[
			'store'=>$store,
			'email'=>$email,
			'customer'=>$customer,
			'order'=>$order,
			'order_items'=>$order_items,
			'hash'=>$hash
			]);
	}


/*============================================================
 *
 *
 *                          CART 
 *
 *
 *============================================================
 */

	/**
	 *
	 *
	 *
	 * @return	mixed
	 */
	public function test_cart_abandoned()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		$customer = Customer::where('customer_email',$email)->first();
		$cart = Cart::where('user_id',1)->first();
		Mail::to($email)->send(new AbandonedCartEmail($store, $email, $cart));
		$hash="1234";
		return view('Mail.RD.cart_abandoned',[
			'store'=>$store,
			'email'=>$email,
			'customer'=>$customer,
			'cart'=>$cart,
			'hash'=>$hash
			]);
	}




/*============================================================
 *
 *
 *                    STOCK NOTIFICATIONS
 *
 *
 *============================================================
 */

	public function test_backinstock()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		$product = Product::find(1);
		$hash="1234";
		return view('Mail.RD.back_in_stock',[
			'store'=>$store,
			'email'=>$email,
			'product'=>$product,
			'hash'=>$hash
			]);
	}





/*============================================================
 *
 *
 *                      SUBSCRIPTIONS
 *
 *
 *============================================================

	/**
	 * GET ROUTE: /test/subscription/confirm
	 *
	 * @return	mixed
	 */
	public function test_subscription_confirm()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new ConfirmSubscription($store, $email));
	}



	/**
	 * GET ROUTE: /test/subscription/confirmed
	 *
	 * @return	mixed
	 */
	public function test_subscription_confirmed()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new SubscriptionConfirmed($store, $email));
	}



	/**
	 * GET ROUTE: /test/subscription/sendwelcome
	 *
	 * @return	mixed
	 */
	public function test_subscription_sendwelcome()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new SendWelcome($store,$email));	
	}



	/**
	 * GET ROUTE: /test/subscription/resend
	 *
	 * @return	mixed
	 */
	public function test_subscription_resend()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new ReSendSubRequest($store,$email));	
	}



	/**
	 * GET ROUTE: /test/subscription/finalrequest
	 *
	 * @return	mixed
	 */
	public function test_subscription_finalrequest()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new FinalRequest($store,$email));	
	}




/*============================================================
 *
 *
 *                          ORDERS
 *
 *
 *============================================================
 */

	/**
	 * GET ROUTE: /test/orders/cancelled
	 *
	 * @return	mixed
	 */
	public function test_order_cancelled()
	{
		$store = app("store");
		$email = Config::get("app.test_email");
		$order = Order::find(28);
		Mail::to($email)->send(new OrderCancelledEmail($store, $email, $order));
		dd($this);
	}

	/**
	 * GET ROUTE: /test/orders/dispatched
	 *
	 * @return	mixed
	 */
	public function test_order_dispatched()
	{
		$store = app("store");
		$email = Config::get("app.test_email");
		$order = Order::find(28);
		Mail::to($email)->send(new OrderDispatchedEmail($store, $email, $order));
		dd($this);
	}



	/**
	 * GET ROUTE: /test/order/paid
	 *
	 * @return	mixed
	 */
	public function test_order_paid()
	{
		$store = app("store");
		$email = Config::get("app.test_email");
		$order = Order::find(28);
		Mail::to($email)->send(new OrderPaidEmail($store, $email, $order));
		dd($this);
	}



	/**
	 * GET ROUTE: /test/order/placed
	 *
	 * @return	mixed
	 */
	public function test_order_placed()
	{
		$store = app("store");
		$email = Config::get("app.test_email");
		$order = Order::find(28);
		Mail::to($email)->send(new OrderPlacedEmail($store, $email, $order));
		dd($this);
	}



	/**
	 * GET ROUTE: /test/order/pending
	 *
	 * @return	mixed
	 */
	public function test_order_pending()
	{
		$store = app("store");
		$email = Config::get("app.test_email");
		$order = Order::find(28);
		Mail::to($email)->send(new OrderPendingEmail($store, $email, $order));
		dd($this);
	}



	/**
	 * GET ROUTE: /test/order/onhold
	 *
	 * @return	mixed
	 */
	public function test_order_onhold()
	{
		$store = app("store");
		$email = Config::get("app.test_email");
		$order = Order::find(28);
		Mail::to($email)->send(new OrderOnHoldEmail($store, $email, $order));
		dd($this);
	}



	public function test_outofstock()
	{
		$products = Product::where('prod_qty',0)->get();
		$store = app('store');
		$email = Config::get("app.test_email");
		echo "<table>";
		foreach($products as $p)
		{
			echo "<tr><td>".$p->id."</td><td></td><tr>";
		}
		echo "</table>";
		$cmd = new OutOfStockJob($store,$email,$product);
		dispatch($cmd);
	}



# URL add ->  /mailrun
	public function mailrun()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		$subject = "TEST - Empty handed on Valentines day?";

		$hash = "";
		#
		# 2018-04-04 uses mailable classes
		#
		Mail::to($email)->send(new MailOut($store, $email, $subject, "valentines-day", $hash));

		$filename = "mail-run";
		##### dispatch(new MailRun($store,$email,$subject,$filename));
	}




}
