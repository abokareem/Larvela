<?php
/**
 * \class	UpdateCartLocks
 * \date	2017-09-13
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
 * \addtogroup CRON
 * Iterate through the 'product_locks' table, get all rows and see if any locks are stale.
 * Remove them and increment the product qty by the value stored in the row.
 * Only change if the product is not an unlimited virtual or parent product.
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use DB;
use App\Models\Product;
use App\Models\ProductLock;
use App\Services\Products\ProductFactory;
use App\Traits\Logger;


/**
 * \brief Iterate through the 'product_locks' table get
 * all rows and see if any locks are stale and remove them,
 * then increment the product qty by the value stored in the
 * row and wrap it all in a database transaction.
 */
class UpdateCartLocks extends Job implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;
use Logger;



    /**
     *============================================================
     * Create a new job instance.
     *============================================================
	 *
	 *
     * @return void
     */
    public function __construct()
    {
		$this->setFileName("larvela-cron");
		$this->setClassName("UpdateCartLocks");
		$this->LogStart();
    }



	/**
     *============================================================
	 * Close off the log
     *============================================================
	 *
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}





    /**
	 *============================================================
     * Entry point from cron, execute the job by calling Run()
	 *============================================================
	 *
	 *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("handle()");
		$this->Run();
		return 0;
    }


	/**
	 *============================================================
	 * Get all product locks in table and decrement, count
	 *============================================================
	 *
	 *
	 * @return	void
	 */

	public function Run()
	{
		$this->LogFunction("Run()");

		$stale_time = time()-300;

		$product_locks = ProductLock::all();
		$this->LogMsg("Product Locks in table [".sizeof($product_locks)."]");
		if(sizeof($product_locks)>0)
		{
			$this->LogStart();
			$this->LogMsg("Check time is [".$stale_time."]");
			foreach($product_locks as $locked)
			{
				$this->LogMsg("Checking product_locks row ID [".$locked->id."]");
				if($locked->product_lock_utime < $stale_time)
				{
					$this->LogMsg("product_locks row ID [".$locked->id."] is stale - remove");
					$this->LogMsg("Product ID [".$locked->product_lock_pid."]");
					$product = Product::find($locked->product_lock_pid);
					$locked_qty = $locked->product_lock_qty;
					$stock_qty = $product->prod_qty;
					$this->LogMsg("Product QTY [".$stock_qty."] add back [".$locked_qty."]");
	
					\DB::beginTransaction();
					$this->LogMsg("Updating Product [".$product->id."]");
					
					$qty = $stock_qty + $locked_qty;
					$product->prod_qty = $qty;
					$product->save();

					$this->IncProductQty($product, $qty);
					$this->LogMsg("Removing Lock [".$locked->id."]");
					$locked->delete();
					\DB::commit();
					$this->LogMsg("TX Complete.");
				}
			}
		}
		else
		{
			$this->LogMsg("No products locked at present... skipping.");
		}
	}



	/**
	 * Increment the qty if the Product supports this.
	 * Need to check the ProductController if supported.
	 *
	 * @param	mixed	$product
	 * @param	integer	$qty
	 * @return	void
	 */
	protected function IncProductQty($product, $newqty)
	{
		$controller = ProductFactory::build($product->prod_type);
	}
}
