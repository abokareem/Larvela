<?php
/**
* \class	UpdateCartLocks
 *\date		2017-09-13
 *
 * \addtogroup CRON
 * Iterate through the 'product_locks' table, get all rows and see if any locks are stale.
 * Remove them and increment the product qty by the value stored in the row.
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Product;
use App\Models\ProductLock;
use App\Traits\Logger;
use DB;

/**
 * \brief Iterate through the 'product_locks' table get
 * all rows and see if any locks are stale and remove them,
 * then increment the product qty by the value stored in the
 * row and wrap it all in a database transaction.
 */
class UpdateCartLocks implements ShouldQueue
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
		$this->setFileName("larvela-cron");
		$stale_time = time()-300;
		$product_locks = ProductLock::all();
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
					$product->prod_qty = $stock_qty + $locked_qty;
					$product->save();
					$this->LogMsg("Removing Lock [".$locked->id."]");
					$locked->delete();
					\DB::commit();
					$this->LogMsg("TX Complete.");
				}
			}
			$this->LogEnd();
		}
	}
}
