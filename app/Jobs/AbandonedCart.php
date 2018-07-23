<?php
/**
 * \class	AbandonedCart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-08-30
 *
 * [CC]
 *
 * \addtogroup	 MIGRATE
 * AbandonedCart - Send a templated email after the Customer has abandond the cart for a period of time.
 * Does not do anything to the cart.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;



/**
 * @brief Place holder for any business logic needed when a Cart Has been abandonded 1 day later.
 */
class AbandonedCart extends Job 
{



    /**
     * Create a new job instance save store and email details away.
	 *
     * @param  $store	mixed - The Store object
     * @param  $email	string - email address of customer
     * @param  $cart	mixed - The Cart onject
     * @return void
     */
    public function __construct($store, $email, $cart)
    {
    }



    /**
	 * Pleace holder for aditional business logic to run prior to customer notification.
	 * - May run as Queue Job, so may execute before/durng or after Email gets sent.
     * @return void
     */
    public function handle()
    {
    }
}
