<?php
/**
 * \class	AbandonedWeekOldCart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-08-30
 *
 *
 *
 * \addtogroup	Cart_Abandonment
 * AbandonedWeekOldCart - Place holder for additonal business Logic.
 * - CRON Calls this via ProcessAbandonedCart Job.
 * - Cart has been abandoned for 1 week now.
 * - Email sent via a Mailable Job.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\Jobs\EmailUserJob;

use App\Models\Cart;
use App\Models\Store;
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
     * Save passed in parameters for later use. 
	 *
     * @param  $store	mixed - The Store object.
     * @param  $email	string - email address of customer.
     * @param  $cart	mixed - The Cart object.
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
