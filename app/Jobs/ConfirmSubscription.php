<?php
/**
 * \class	ConfirmSubscription
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-16
 *
 * [CC]
 *
 * \addtogroup	Subscription
 * ConfirmSubscription - A customer has visited the site and entered their email address in a subscription box,
 * in this job we can add additional business logic.
 */
namespace App\Jobs;


use Hash;
use App\Jobs\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;


use App\Models\Store;
use App\Models\Customer;
use App\Models\SubscriptionRequest;


use App\Traits\Logger;


/**
 * \brief Send a templated email asking for confirmation of the email address and subscription to site.
 */
class ConfirmSubscription implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;
use Logger;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $email
 */
protected $email;






    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using CONFIRM_SUBSCRIPTION action 
	 *
     * @param  $store     mixed - store data collection
     * @param  $email     string - email address of customer
     * @return void
     */
    public function __construct($store, $email)
    {
		$this->setFileName("store-jobs");
		$this->LogStart();
		$this->store = $store;
		$this->email = $email;
    }



    /**
     * Add an entry to the subscription table and execute any additional business logic.
	 *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("ConfirmSubscription::handle()");

		$this->SubscribeEmail($this->email);
		
		$from = $this->store->store_sales_email;
		$store_name = $this->store->store_name;
		$subject = "[LARVELA] Subscription Confirmation Request Sent To [".$this->email."]";
		$text = "Subscription confirmation request sent to [".$this->email."]";
		dispatch(new EmailUserJob(Customer::find(1)->customer_email, [$from=>$store_name], $subject, $text));
    }





	/**
	 * Subscribe the user to the subscription request table
	 * insert defaults will fill in the needed values.
	 *
	 * PROCESS_VALUE
	 * 0 - sent,
	 * 1 - 24hr resend,
	 * 2 > countdown to delete.
	 *
	 * CRON jobs will then prompt at different times using the data supplied.
	 */
	protected function SubscribeEmail($email )
	{
		$o = new SubscriptionRequest;
		$o->sr_email = strtolower($email);
		$o->sr_status = 'W';
		$o->sr_process_value = 0;
		$o->sr_date_created = date("Y-m-d");
		$o->sr_date_updated = date("Y-m-d");
		$o->save();
	}
}
