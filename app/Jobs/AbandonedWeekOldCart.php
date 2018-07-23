<?php
/**
 * \class	AbandonedWeekOldCart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-08-30
 *
 *
 *
 * \addtogroup	CRON
 * AbandonedWeekOldCart - Place holder for additonal business Logic.
 * - Cart has been abandoned for 1 week now.
 */
namespace App\Jobs;


use Hash;
use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\Jobs\EmailUserJob;

use App\Models\Customer;




/**
 * \brief Cart has been abandoned for 1 week, execute any additional business logic here.
 */
class AbandonedWeekOldCart implements ShouldQueue
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
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using CONFIRM_SUBSCRIPTION action 
	 *
     * @param  $store     mixed - store data collection
     * @param  $email     string - email address of customer
     * @param  $hash_url  string - hashed URL string to substitue in.
     * @return void
     */
    public function __construct($store, $email, $cart)
    {
		$this->store = $store;
		$this->email = $email;
		$this->cart  = $cart;
    }



    /**
     * Fetch the template, parse with the store helper and send to customer
	 *
     * @return void
     */
    public function handle()
    {
		$from = $this->store->store_sales_email;	
		$subject = "[LARVELA] Week Old Abandoned cart message sent to [".$this->email."]";
		$text = "Notice: Cart ".$this->cart->id." abandoned 1 week ago by customer ".$this->email;

		$admin_user = Customer::find(1);
		dispatch(new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
    }
}
