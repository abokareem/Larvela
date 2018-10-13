<?php
/**
 * \class	SubscriptionConfirmed
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-20
 *
 * [CC]
 *
 * \addtogroup Subscription 
 * SubscriptionConfirmed - Customer has clicked the URL link in the previously sent email and confirmed their email address.
 * We can now update the subscription table record and send them a welcome email.
 */
namespace App\Jobs;


use Hash;
use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;

use App\Models\Store;
use App\Models\Customer;
use App\Models\SubscriptionRequest;

use App\Traits\TemplateTrait;
use App\Traits\Logger;

/** 
 * \brief Send an email confirming subscription to site welcoming new user.
 *
 * Job flow is:
 * CONFIRM_SUBSCRIPTION ---> SUBCRIPTION_CONFIRMED
 *                      \---> PLEASE_CONFIRM (CRON)
 *
 * CRON -- 1 day --> PLEASE_CONFIRM
 *      -- 2 day --> Delete entry no confirmation received.
 *
 * Notes:
 * ======
 * Use helpers to render the template body.
 */
class SubscriptionConfirmed extends Job
{
use TemplateTrait;
use Logger;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/**
 * The users email that subscribed
 * @var object $store
 */
protected $email;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $to
 */
protected $to;


/**
 * Email address to send from
 * @var string $from
 */
protected $from;


/**
 * The subject line for this email
 * @var string $subject
 */
protected $subject;


/**
 * The filename and path for email template to use.
 * Note: Template may also be made of SEOHelper calls.
 * @var string template_file_name
 */
protected $template_file_name;


/**
 * The tempate action 
 * @var string $ACTION
 */
private $ACTION="SUBSCRIPTION_CONFIRMED";

    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using CONFIRM_SUBSCRIPTION action 
	 *
     * @param  $store mixed store data collection
     * @param  $email string email address of customer
     * @return void
     */
    public function __construct($store,$email)
    {
		$this->to = $email;
		$this->email = $email;
		$this->store = $store;
		$this->subject = "Subscription Confirmed";
		$this->from = $this->store->store_sales_email;

		$this->template_file_name = $this->getTemplate($this->ACTION);
		if(strlen($this->template_file_name)==0)
		{
			$this->template_file_name = strtolower($this->ACTION).".email";
		}
		$this->MarkAsConfirmed($email);
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using Swift Mailer. 
	 *
	 * {FIX_2017-11-05} SubscriptionConfirmed.php - remove StrReplace call, added translate.
	 *
     * @return void
     */
    public function handle()
    {
		$Store = new Store;
		$Customer = new Customer;
		$SubscriptionRequest = new SubscriptionRequest;
		#
		# {FIX_2017-10-25} SubscriptionConfirmed.php - handle() - refactor code to use Eloquent
		#
		$record = SubscriptionRequest::where('sr_email',$this->email)->first();
		$hash_value = hash('ripemd160', $this->email.$this->store->store_env_code);

		$file = $this->getTemplatePath($this->store).$this->template_file_name;

		$text = "<h1>WARNING</h1><p>Subscription Request email NOT sent to: ".$this->to."</p><br/><br/>Check Template file! - <b>".$file."</b>";
		if(file_exists($file))
		{
			$text = file_get_contents($file);

			$body1 = str_replace("{CIDHASH}", $hash_value, $text);
			$body = str_replace("{CID}", $record->id, $body1);

			$store_name_l = strtoupper($this->store->store_name);
			$store_name = str_replace(" ","_", $store_name_l);
			$header_tag = $store_name."_EMAIL_HEADER";
			$footer_tag = $store_name."_EMAIL_FOOTER";

			$header = SEOHelper::getText($header_tag);
			$footer = SEOHelper::getText($footer_tag);

			$t1 = $header.$body.$footer;
			$t2 = $Store->translate($t1, $this->store);
			$text = str_replace("{CUSTOMER_EMAIL}",$this->to,$t2);

			$cmd = new EmailUserJob($this->to, $this->from, $this->subject, $text);
			dispatch($cmd);
		}
		#
		# {FIX_2017-10-25} SubscriptionConfirmed.php - handle() - refactor code to use Eloquent
		#
		$admin_user = Customer::find(1);
		$admin_email = $admin_user->customer_email;
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Subscription Confirmed message sent to [".$this->to."]", $text);
		dispatch($cmd);
    }




	/**
	 * Mark the subscribption request as Completed
	 *
	 * @param	string	$email
	 * @return	void
	 */
	protected function MarkAsConfirmed($email)
	{
		$o = SubscriptionRequest::where('sr_email',$email)->first();
		$o->sr_status = 'C';
		$o->sr_process_value = 0;
		$o->sr_date_updated = date("Y-m-d");
		$o->save();
    }
}
