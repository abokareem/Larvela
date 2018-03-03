<?php
/**
 * \class	EmptyCartJob
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-01
 *
 * [CC]
 *
 * \addtogroup Internal
 * EmptyCartJob - Deletes all cart items and cart data.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Traits\Logger;

use	App\Models\Cart;
use	App\Models\CartItem;
use	App\Models\CartData;


/**
 * \brief Deletes all cart items and the cart data row. Updates the cart last access date accordingly.
 */
class EmptyCartJob extends Job 
{
use Logger;


/**
 * The row ID of the cart.
 * @var int $cart_id
 */
protected $cart_id;



    /**
     * Create a new job instance.
     *
     * @param	integer	$cart_id	The row ID from the carts table
     * @return	void
     */
    public function __construct($cart_id)
    {
		$this->setFileName("store");
		$this->LogStart();
		$this->LogMsg("CLASS::EmptyCartJob");

		$this->cart_id = $cart_id;
    }



    /**
     * Log the job has cleaned up
     *
     * @return	void
     */
	public function __destruct()
	{
		$this->LogMsg("CLASS::EmptyCartJob");
		$this->LogEnd();
	}




    /**
     * Execute the job, remove all items and data, then update the cart "updated_at" timestamp.
     *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("handle()");

		$cart_id = $this->cart_id;

		$cart = Cart::find($cart_id);
		
		$items = CartItem::where('cart_id',$cart_id)->get();
		$this->LogMsg("Remove cart items.");
		foreach($items as $item)
		{
			$this->LogMsg("\-- Removing cart item ID [".$item->id."]");
			$item->delete();
		}

		$data = CartData::where('cd_cart_id',$cart_id)->get();
		foreach($data as $d)
		{
			$d->delete();
		}
		$this->LogMsg("Cart Data and Items now removed");
		$cart->touch();
	}
}
