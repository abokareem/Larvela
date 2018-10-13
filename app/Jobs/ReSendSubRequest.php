<?php
/**
 * \class	ReSendSubRequest
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-01-10
 *
 * [CC]
 *
 * \addtogroup Subscription
 * ReSendSubRequest - Used by the CRON automation job to resent a subscription request at some point later.
 */
namespace App\Jobs;


use Hash;
use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;

use App\Models\Customer;
use App\Models\Store;
use App\Models\SubscriptionRequest;

use App\Traits\TemplateTrait;
use App\Traits\Logger;


/**
 * \brief Re-Send templated email asking for confirmation of the email address and subscription to site.
 *
 * Workflow is:
 * CONFIRM_SUBSCRIPTION ---> SUBCRIPTION_CONFIRMED
 *                      \---> PLEASE_CONFIRM (CRON) <---- ReSendSubRequest
 * Requests "CONFIRM_SUBSCRIPTION" template from template_mapping table.
 * {INFO_2017-10-28} Moved template to ./store_env_code/...
 * {INFO_2018-03-10} Changed to use subscription_requests table.
 */
class ReSendSubRequest extends Job 
{
use TemplateTrait;
use Logger;

/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $to
 */
protected $email;


/**
 * Email address to send from.
 * @var string $from
 */
protected $from;


/**
 * A URL sgring containing the hashed values to be confirmed
 * @var string $hased_url
 */
protected $hashed_url;


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


private $ACTION="RESEND_SUB_REQUEST";



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
		$sales_email = $store->store_sales_email;
		$this->from = ["$sales_email"=>"Sales Team"];
		$this->subject = "Subscription Confirmation Required";

		$this->hashed_url = hash('ripemd160', $email.$store->store_env_code);

		$this->template_file_name = $this->getTemplate($this->ACTION);
		if(strlen($this->template_file_name)==0)
		{
			$this->template_file_name = strtolower($this->ACTION).".email";
		}
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using another Job.
	 *
     * @return void
     */
    public function handle()
    {
		$Store = new Store;

		$file = $this->getTemplatePath($this->store).$this->template_file_name;
		$subscription_request = SubscriptionRequest::where('sr_email', $this->email )->first();
		$cid = 0;
		$text = "<h1>WARNING</h1><p>Resend Subscription Request email NOT sent to: ".$this->email."</p><br/><br/>Check Template file! - <b>".$file."</b>";
		if(!is_null($subscription_request))
		{
			$cid = $subscription_request->id;
			if(file_exists($file))
			{
				$text = file_get_contents($file);
	
				$b1 = str_replace("{CIDHASH}", $this->hashed_url, $text);
				$body = str_replace("{CID}", $cid, $b1);
	
				$store_name_lower = strtoupper($this->store->store_name);
				$store_name = str_replace(" ","_", $store_name_lower);
				$header_tag = $store_name."_EMAIL_HEADER";
				$footer_tag = $store_name."_EMAIL_FOOTER";
	
				$header = SEOHelper::getText($header_tag);
				$footer = SEOHelper::getText($footer_tag);
		
				$t1 = $header.$body.$footer;
				$t2 = $Store->translate($t1, $this->store);
				$text = str_replace("{CUSTOMER_EMAIL}",$this->email,$t2);
	
				$cmd = new EmailUserJob($this->email, $this->from, $this->subject, $text);
				dispatch($cmd);
				$admin_user = Customer::find(1);
				$admin_email = $admin_user->customer_email;
				$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Resend Subscription Request message sent to [".$this->email."]", $text);
				dispatch($cmd);
				return true;
			}
		}
		$admin_user = Customer::find(1);
		$admin_email = $admin_user->customer_email;
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Resend Subscription Request message NOT sent to [".$this->email."]", $text);
		dispatch($cmd);
		return true;
    }
}
