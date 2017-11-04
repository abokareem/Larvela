<?php
/**
 * \class	LoginFailedEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-09-04
 *
 * [CC]
 *
 * \addtogroup Security
 * LoginFailedEmail - Send an email to the user that a failed login attempt occured using their email address.
 */
namespace App\Jobs;


use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Models\Customer;
use App\Models\Store;


use App\Jobs\EmailUserJob;
use App\Traits\TemplateTrait;


/**
 * \brief Send an email warnign of a failed login attempt.
 * {INFO_2017-10-28} LoginFailedEmail.php - Moved template to ./store_env_code/...
 */
class LoginFailedEmail extends Job 
{
use TemplateTrait;



/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/**
 * The email address to send the email to.
 * @var string $to
 */
protected $to;


/**
 * The email address sent from.
 *
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
 * The template to use
 * @var string $ACTION
 */
private $ACTION="LOGIN_FAILED";


    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using ACTION tag.
	 *
     * @param 	mixed	$store	store data collection
     * @param	string	$email	email address of customer
     * @param 	mixed	$product
     * @return	void
     */
    public function __construct($store, $email)
    {
		$this->to = $email;
		$this->store = $store;
		$this->subject = "WARNING - Failed Login Attempt!";
		$this->from = $this->store->store_sales_email;

		$this->template_file_name = $this->getTemplate($this->ACTION);
		echo "Template: ". $this->template_file_name. "</br>";
		if(strlen($this->template_file_name)==0)
		{
			$this->template_file_name = "template_1_".strtolower($this->ACTION).".email";
		}
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using Swift Mailer. 
	 *
	 * {FIX_2017-11-05} - LoginFailedEmail.php - removed call to StrReplace
	 *
     * @return void
     */
    public function handle()
    {
		$Store = new Store;
		
		$path = base_path();
		$file = $path."/templates/".$this->store->store_env_code."/".$this->template_file_name;
		$text = "<h1>WARNING</h1><p>Login Failed email NOT sent to: ".$this->to."</p><br/><br/>Check Template file! - <b>".$file."</b>";
		if(file_exists($file))
		{
			$body = file_get_contents($file);

			$store_name_lower = strtoupper($this->store->store_name);
			$store_name = str_replace(" ","_", $store_name_lower);
			$header_tag = $store_name."_EMAIL_HEADER";
			$footer_tag = $store_name."_EMAIL_FOOTER";

			$header = SEOHelper::getText($header_tag);
			$footer = SEOHelper::getText($footer_tag);

			$t1 = $header.$body.$footer;
			$t2 = $Store->translate($t1, $this->store);
			$text = str_replace("{EMAIL}", $this->to, $t2);

			$cmd = new EmailUserJob($this->to, $this->from, $this->subject, $text);
			dispatch($cmd);
		}

		#
		# Send to the store administyrator
		#
		$admin_user = Customer::where('id',1)->first();
		$admin_email = $admin_user->customer_email;
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Failed Email Sent to [".$this->to."]", $text);
		dispatch($cmd);
    }
}
