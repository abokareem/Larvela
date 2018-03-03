<?php
/**
 * \class	OutOfStockJob
 * \date	2016-12-06
 * \author	Sid Young <sid@off-grid-engineering.com>
 *
 * [CC]
 *
 * \addtogroup ProductReplenishment
 * OutOfStockJob - Email notification for store admins that the product is now at qty 0.
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



/**
 * \brief Send an Out of stock notification to the store owner when the last of a product is purchased.
 * Also sends an email to the administration user (customer ID=1).
 * {INFO_2017-10-28} "OutOfStockJob.php" - Moved template to ./store_env_code/...

 */
class OutOfStockJob extends Job 
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
 * The Product details
 * @var  mixed $product
 */
protected $product;


/**
 * The template to get
 * @var string $ACTION
 */
private $ACTION="PRODUCT_OUT_OF_STOCK";

    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using PRODUCT_OUT_OF_STOCK action 
	 *
     * @param	mixed	$store		store data collection
     * @param	string	$email		email address of customer
     * @param	mixed	$product
     * @return	void
     */
    public function __construct($store, $email, $product)
    {
		$this->store = $store;
		$this->product = $product;

		$this->to = $email;
		$this->subject = "Product out of Stock!";
		$this->from = $this->store->store_sales_email;

		$this->template_file_name = $this->getTemplate($this->ACTION);
		echo "Template: ". $this->template_file_name. "</br>";
		if(strlen($this->template_file_name)==0)
		{
			$this->template_file_name = strtolower($this->ACTION).".email";
		}
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using Swift Mailer. 
	 *
	 * {FIX_2017-11-05} - OutOfStockJob.php - removed call to StrReplace, refactored code.
	 *
     * @return void
     */
    public function handle()
    {
		$Customer = new Customer;
		$Product = new Product;
		$Store = new Store;

		$path = base_path();
		$file = $path."/templates/".$this->store->store_env_code."/".$this->template_file_name;
		$text = "Product Out Of Stock Template not loadable! - ".$file;
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
			$t2 = $Product->translate($t1, $this->product);
			$text = $Store->translate($t2, $this->store);

			$cmd = new EmailUserJob($this->to, $this->from, $this->subject, $text);
			dispatch($cmd);
		}

		$admin_user = Customer::find(1);
		$admin_email = $admin_user->customer_email;
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] OutofStock Notification", $text);
		dispatch($cmd);
    }
}
