<?php
/**
 * \class	ConfirmSubscription
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-16
 *
 * [CC]
 *
 * \addtogroup	WelcomeProgram
 * ConfirmSubscription - A customer has visited the site and entered their email address in a subscription box,
 * we now need to send them a confirmation email with a link.
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
use App\Traits\GuidTrait;


/**
 * \brief Send a templated email asking for confirmation of the email address and subscription to site.
 *
 * Workflow is:
 *  
 * CONFIRM_SUBSCRIPTION ---> SUBCRIPTION_CONFIRMED
 *                      \---> PLEASE_CONFIRM (CRON)
 * Followup workflow is:
 *
 * CRON -- 1 day --> PLEASE_CONFIRM
 *      -- 2 day --> PLEASE_CONFRIM
 *
 * Requests "CONFIRM_SUBSCRIPTION" template fromt emplate_mapping table.
 *
 * {INFO_2017-10-28} Moved template to ./store_env_code/...
 * {FIX_2017-11-05} - Added Store translate methods and code refactor
 */
class ConfirmSubscription extends Job 
{
use TemplateTrait;
use Logger;
use GuidTrait;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/**
 * The email address to send from
 * @var string $from
 */
protected $from;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $to
 */
protected $to;


/**
 * A URL sgring containing the hashed values to be confirmed
 * @var string $hased_url
 */
protected $hash_value;


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
 *
 * @var integer $sub_id
 */
protected $sub_id;




private $ACTION="CONFIRM_SUBSCRIPTION";



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
		$this->setFileName("store-jobs");
		$this->LogStart();
		$this->store = $store;
		$this->to = $email;

		$this->sub_id = 0;
		$this->hash_value = hash('ripemd160', $email.$store->store_env_code);

		$this->subject = "Subscription Confirmation Required";
		$this->from = $this->store->store_sales_email;

		$this->template_file_name = $this->getTemplate($this->ACTION);

		if(strlen($this->template_file_name)==0)
		{
			$this->template_file_name = strtolower($this->ACTION).".email";
		}
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using another Job. insert GUID into text, this will be referenced back to our
	 * database later to match the email.
	 *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("ConfirmSubscription::handle()");

		$SubscriptionRequest = new SubscriptionRequest;
		$Customer = new Customer;
		$Store = new Store;
		
		$subscription_request = SubscriptionRequest::where('sr_email',$this->to)->first();
		$this->sub_id = $subscription_request->id;

		$path = base_path();
		$file = $path."/templates/".$this->store->store_env_code."/".$this->template_file_name;
		$text = "<h1>WARNING</h1><p>Subscription Request email NOT sent to: ".$this->to."</p><br/><br/>Check Template file! - <b>".$file."</b>";
		$this->LogMsg("Template file [".$file."]");
		if(file_exists($file))
		{
			$this->LogMsg("Loading Template.");
			$text = file_get_contents($file);
		
			$body1 = str_replace("{CIDHASH}", $this->hash_value, $text);
			$body = str_replace("{CID}", $this->sub_id, $body1);

			$store_name_lower = strtoupper($this->store->store_name);
			$store_name = str_replace(" ","_", $store_name_lower);
			$header_tag = $store_name."_EMAIL_HEADER";
			$footer_tag = $store_name."_EMAIL_FOOTER";

			$header = SEOHelper::getText($header_tag);
			$footer = SEOHelper::getText($footer_tag);

			$t1 = $header.$body.$footer;
			$text = $Store->translate($t1, $this->store);

			$cmd = new EmailUserJob($this->to, $this->from, $this->subject, $text);
	        dispatch($cmd);
		}
		else
		{
			$this->LogError("Unable to Load Template!");
			$this->LogError("Email NOT sent");
		}
		$admin_user = Customer::find(1);
		$admin_email = $admin_user->customer_email;
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Subscription Confirmation Request Sent To [".$this->to."]", $text);
		dispatch($cmd);
    }





	/**
	 * Subscribe the user to the subscription request table
	 * insert defaults will fill in the needed values.
	 *
	 * CRON jobs will then prompt at different times using the data supplied.
	 */
	protected function SubscribeEmail($email )
	{
		$SubscriptionRequest = new SubscriptionRequest;
		return $SubscriptionRequest->InsertSubscription( $email );
	}
}
