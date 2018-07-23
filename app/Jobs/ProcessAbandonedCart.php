<?php
/**
 * \date	2017-09-14
 * \author	Sid Young
 * \class	ProcessAbandonedCart
 *
 * \addtogroup CRON
 * Retrieve all the cart items and if:
 * - Any dated yesterday then send AbandonedCartEmail.
 * - Any dated a week old, send AbandonedWeekOldCartEmail.
 * - Run ONCE a day!
 */
namespace App\Jobs;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\AbandonedCart;
use App\Mail\AbandonedWeekOldCart;

use App\Jobs\AbandonedCart;
use App\Jobs\AbandonedWeekOldCart;


use App\Models\Users;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Store;


use App\Traits\Logger;
use DB;


class ProcessAbandonedCart implements ShouldQueue
{
use Logger;

    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$this->Run();
    }



	public function Run()
	{
		$this->setFileName("store-cron");
		
		$found=0;
		$yesterday = date("Y-m-d", strtotime("-1 days"));
		$last_week = date("Y-m-d", strtotime("-7 days"));

		$store = app('store');

		$fc1 = array();
		$fc7 = array();

		$cartitems = CartItem::all();
		if(sizeof($cartitems)>0)
		{
			$this->LogStart();
			#
			# Check for abandond cart 24 hours old
			#
			$this->LogMsg("24hr abandoned cart check");
			foreach($cartitems as $item)
			{
				$item_date = date("Y-m-d", strtotime($item->updated_at));
				if($item_date == $yesterday)
				{
					if(in_array($item->cart_id,$fc1)==true)
					{
						$this->LogMsg("Skipping cart [".$item->cart_id."] - already processed");
					}
					else
					{
						$cart = Cart::find($item->cart_id);
						$user = Users::find($cart->user_id);
						$found++;
						$this->LogMsg("Process cart ID [".$item->id."] - Customer [".$user->email."]");
						$this->LogMsg("Dispatch Job");
						dispatch(new AbandonedCart($store, $user->email, $cart));
						$this->LogMsg("Dispatch EMail");
						Mail::to($user->email)->send(new AbandonedCartEmail($store, $user->email, $cart));
						$this->LogMsg("Save cart id");
						#
						# save the id so we dont send again
						#
						array_push($fc1,$item->cart_id);
					}
				}
			}
			$this->LogMsg("24hr check found ".$found." abandoned carts!");

			#
			# Now do for 7 day old carts
			#
			$found = 0;
			$this->LogMsg("7 day abandoned cart check");
			foreach($cartitems as $item)
			{
				if($item_date == $last_week)
				{
					if(in_array($item->cart_id,$fc7)==true)
					{
						$this->LogMsg("Skipping cart [".$item->cart_id."] - already processed");
					}
					else
					{
						$cart = Cart::find($item->cart_id);
						$user = Users::find($cart->user_id);
						$found++;
						$this->LogMsg("Process cart ID [".$item->id."] - Customer was [".$user->email."]");
						$this->LogMsg("Dispatch Job");
						dispatch(new AbandonedWeekOldCart($store, $user->email, $cart));
						$this->LogMsg("Dispatch EMail");
						Mail::to($user->email)->send(new AbandonedWeekOldCartEmail($store, $user->email, $cart));
						array_push($fc7,$item->cart_id);
					}
				}
			}
			$this->LogMsg("7 Day check found ".$found." abandoned carts!");
			$this->LogEnd();
		}
	}
}




