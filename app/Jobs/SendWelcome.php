<?php
/**
 * \class	SendWelcome
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-16
 *
 * [CC]
 *
 * \addtogroup WelcomeProgram
 * SendWelcome - Sends a welcome email after they have subscribed to the web site or registered.
 */
namespace App\Jobs;


use Hash;
use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;


use App\Models\Customer;
use App\Models\Store;

use App\Traits\Logger;


/**
 * \brief Send a templated email welcoming the Customer.
 *
 * Called when MyAccount used and a Customer record is inserted. This implies they have registered
 * on the site but not necessarily subscribed.
 * {INFO_2017-10-28} Moved template to ./store_env_code/...
 */
class SendWelcome extends Job 
{
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
     * @param  $hash_url  string - hashed URL string to substitue in.
     * @return void
     */
    public function __construct($store, $email)
    {
		$this->store = $store;
		$this->email = $email;
    }



    /**
     * Fetch the template, parse with the store helper and send to customer
	 *
	 * {FIX_2017-11-05} - SendWelcome.php - removed call to StrReplace
	 *
     * @return void
     */
    public function handle()
    {
		$subject = "[LARVELA] Welcome email sent to [".$this->email."]";
		$text = "Welcome email sent to [".$this->email."]";
		$from = $this->store->store_sales_email;
		$admin_user = Customer::find(1);
		$admin_email = $admin_user->customer_email;
		dispatch(new EmailUserJob($admin_email, $from, $subject, $text));
    }
}
