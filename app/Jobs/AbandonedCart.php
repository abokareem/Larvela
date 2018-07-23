<?php
/**
 * \class	AbandonedCart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-08-30
 *
 * [CC]
 *
 * \addtogroup CartAbandonment
 * AbandonedCart - Send a templated email after the Customer has abandond the cart for a period of time.
 * Does not do anything to the cart.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Customer;


/**
 * @brief Place holder for any business logic needed when a Cart Has been abandonded 1 day later.
 */
class AbandonedCart implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;

/**
 * The Store object
 * @var mixed $store
 */
protected $store;


/**
 * The email address of the customer
 * @var string $email
 */
protected $email;


/**
 * The Cart object
 * @var mixed $cart
 */
protected $cart;


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
		$this->store = $store;
		$this->email = $email;
		$this->cart  = $cart;
    }



    /**
	 * Pleace holder for aditional business logic to run prior to customer notification.
	 * - May run as Queue Job, so may execute before/durng or after Email gets sent.
     * @return void
     */
    public function handle()
    {
		$text = "Notice: Cart abandoned email sent to: ".$this->email;
		$subject = "[LARVELA] Cart Abandoned email sent to [".$this->email."]";
		$from = $this->store->store_sales_email;

		$admin_user = Customer::find(1);
		dispatch( new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
    }

}
