<?php
/**
 * \class	MailRun
 * \date	2018-02-11
 * \author	Sid Young <sid@off-grid-engineering.com>
 *
 * [CC]
 *
 * \addtogroup BulkEmails
 * MailRun - Send a custom email to a subscriber, done as a product run to all subscriber, controller from a scheduled job.
 */
namespace App\Jobs;

use App\Jobs\Job;
use App\Helpers\SEOHelper;

use App\Models\Customer;
use App\Models\Store;
use App\Models\Product;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;
use App\Traits\TemplateTrait;
use App\Traits\Logger;



/**
 * \brief Send a crafted promotion email to a customer, needs Customer and Store.
 * Product data is in email.
 */
class MailRun extends Job 
{
use TemplateTrait;
use Logger;


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
 * The template to get
 * @var string $ACTION
 */
private $ACTION="MAIL_RUN";

    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using PRODUCT_OUT_OF_STOCK action 
	 *
     * @param	mixed	$store		store data collection
     * @param	string	$email		email address of customer
     * @param	mixed	$product
     * @return	void
     */
    public function __construct($store, $email, $subject, $filename)
    {
		$this->setFileName("store-cron");
		$this->store = $store;
		$this->to = $email;
		$this->subject = $subject;
		$this->from = $this->store->store_sales_email;
		$this->template_file_name = $filename;
    }


	/**
     * Log the job has cleaned up
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS::MailRun");
		$this->LogEnd();
	}



    /**
     * Fetch the template, parse with the store helper and
	 * then send using Swift Mailer. 
	 *
     * @return void
     */
    public function handle()
    {
		$Store = new Store;

		$customer = Customer::where('customer_email', $this->to)->first();
		$path = base_path();
		$file = $path."/templates/".$this->store->store_env_code."/".$this->template_file_name;
		$text = "Mail Run Template not loadable! - ".$file;
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
			$t2 = $customer->translate($t1, $customer);
			$text = $Store->translate($t2, $this->store);

			$cmd = new EmailUserJob($this->to, $this->from, $this->subject, $text);
			dispatch($cmd);
			$this->LogMsg("Email sent to [".$this->to."]" );
			return;
		}
		$this->LogMsg("Failed to send to [".$this->to."] - file [] not found!");
    }
}
