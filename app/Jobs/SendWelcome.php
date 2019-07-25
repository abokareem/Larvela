<?php
/**
 * \class	SendWelcome
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-16
 * \version	1.0.1
 * 
 *
 * \addtogroup WelcomeProgram
 * SendWelcome - Sends a welcome email after they have subscribed to the web site or registered.
 */
namespace App\Jobs;


use Hash;
use App\Jobs\Job;

use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Mail\SendWelcomeEmail;

use App\Models\Store;
use App\Models\Customer;

use App\Traits\Logger;


/**
 * \brief Send a templated email welcoming the Customer.
 *
 * Called when MyAccount used and a Customer record is inserted. This implies they have registered
 * on the site but not necessarily subscribed.
 * {INFO_2017-10-28} Moved template to ./store_env_code/...
 * {INFO_2019-07-23} Implemented Mailable Interface
 */
class SendWelcome extends Job implements ShouldQueue
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
	 *============================================================
     * Create a new job instance save store and email details away.
	 *============================================================
	 *
	 *
     * @param  $store     mixed - store data collection
     * @param  $email     string - email address of customer
     * @return void
     */
    public function __construct($store, $email)
    {
		$this->setFileName("larvela-jobs");
		$this->setClassName("SendWelcome");
		$this->LogStart();
		$this->store = $store;
		$this->email = $email;
    }



    /**
	 *============================================================
     * send to admin user email and customer email
	 *============================================================
	 *
	 * {FIX_2017-11-05} - SendWelcome.php - removed call to StrReplace
	 *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("handle()");

		#
		# place additional business logic here.
		#

		$user = Customer::find(1);
		$this->LogMsg("Send email to [".$user->customer_email."]");
		Mail::to($user->customer_email)->send(new SendWelcomeEmail($this->store, $user->customer_email));
		$this->LogMsg("Send email to [".$this->email."]");
		Mail::to($this->email)->send(new SendWelcomeEmail($this->store, $this->email));
    }
}
