<?php
/**
 * \class	TestMail
 * \date	2018-10-08
 *
 * \addtogroup Testing
 * TestMail - Provides various mail related test routines which will send to the system user.
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

use App\Jobs\LoginFailed;
use App\Mail\LoginFailedEmail;

use App\Jobs\OrderCancelled;
use App\Jobs\OrderCompleted;
use App\Jobs\OrderDispatched;
use App\Jobs\OrderDispatchPending;
use App\Jobs\OrderPaid;
use App\Jobs\OrderPlaced;
use App\Jobs\CheckPendingOrders;

use App\Jobs\OutOfStockJob;
use App\Jobs\BackInStock;
use App\Mail\BackInStockEmail;

use App\Jobs\PostPurchaseEmail;

use App\Jobs\ProcessSubscriptions;
use App\Jobs\ConfirmSubscription;
use App\Mail\ConfirmSubscriptionEmail;
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
use App\Mail\SendWelcomeEmail;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Notification;
use App\Models\User;

use App\Models\Attribute;
use App\Models\AttributeProduct;
use App\Models\AttributeValue;


use App\Models\Category;
use App\Models\Image;
use App\Models\CategoryImage;

use App\Events\Larvela\AddToCartMessage;
use App\Events\Larvela\DispatcherFactory;



class TestMail extends Controller
{



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
		Mail::to($email)->send(new ConfirmSubscriptionEmail($store, $email));
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
		Mail::to($email)->send(new SendWelcomeEmail($store, $email));
		dd($this);
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




/*============================================================
 *
 *
 *                          Stock
 *
 *
 *============================================================
 */

	/**
	 *
	 *
	 * GET ROUTE: /test/stock/outofstock
	 *
	 */
	public function test_stock_outofstock()
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


	/**
	 *
	 *
	 * GET ROUTE: /test/stock/backinstock
	 *
	 */
	public function test_stock_backinstock()
	{
		$store = app('store');
		$email = Config::get("app.test_email");
		$notify_list = Notification::all();

		echo "<table>";
		$products = array();
		foreach($notify_list as $notification)
		{
			$product = Product::where('prod_sku',$notification->product_code)->first();
			echo "<tr><td>".$product->id."</td><td>".$product->prod_sku."</td><td>".$notification->email_address."<tr>";
			dispatch(new BackInStock($store,$email,$product));
		}
		echo "</table>";
	}



/*============================================================
 *
 *
 *                          Login Failed
 *
 *
 *============================================================
 */

	public function test_login_failed()
	{
		$store = app('store');
		$email = Config::get("app.test_email");
		dispatch(new LoginFailed($store,$email));
	}


	public function test_dispatch_email()
	{
		$store = app('store');
		$cart = Cart::first();
		$product = Product::first();
		$user = User::find(1);
		$m = new AddToCartMessage($store,$user,$cart,$product);
		$m->dispatch();
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
