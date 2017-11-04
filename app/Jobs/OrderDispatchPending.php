<?php
/**
 * \class	OrderDispatchPending
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-22
 *
 * [CC]
 *
 * \addtogroup Transactional
 * OrderDispatchPending - Notify the customer that their order has been dispatched.
 */
namespace App\Jobs;

use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Models\Customer;

use App\Jobs\EmailUserJob;
use App\Traits\TemplateTrait;


/**
 * \brief Send a templated email confirming dispatching of the order.
 * Fetch and send the templated email, template mapping is: "ORDER_DISPATCHED"
 */
class OrderDispatchPending extends Job 
{
use TemplateTrait;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $to
 */
protected $to;

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
 * Tempalte to use
 * @var string $ACTION
 */
private $ACTION="ORDER_DISPATCH_PENDING";



    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using ORDER_DISPATCHED action 
	 *
     * @param	mixed	$store	store data collection
     * @param	string	$email	email address of customer
     * @param	mixed	$order
     * @return	void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->order = $order;

		$this->to    = $email;
		$this->from  = $store->store_sales_email;

		#
		# Add order number to the SUbject and details from the order in the handle() method
		#
		$this->subject = "Order Dispatch Pending - Order ID ".$order->id;

		$this->template_file_name = $this->getTemplate($this->ACTION);
		echo "Template: ". $this->template_file_name. "</br>";
		if(strlen($this->template_file_name)==0)
		{
			$this->template_file_name = "template_1_".strtolower($this->ACTION).".email";
		}
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using Job Dispatch.
	 *
	 * {FIX_2017-11-05} - OrderDispatchPending.php - removed call to StrReplace
	 *
     * @return void
     */
    public function handle()
    {
		$Customer = new Customer;
		$Store = new Store;
		$path = base_path();
		$file = $path."/templates/".$this->store->store_env_code."/".$this->template_file_name;

		$text = "<h1>WARNING</h1><p>Order Dispatch Pending email NOT sent to: ".$this->to."</p><br/><br/>Check Template file! - <b>".$file."</b>";
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
			$text = $Store->translate($t1, $this->store);

			$cmd = new EmailUserJob($this->to, $this->from, $this->subject, $text);
			dispatch($cmd);
		}

		$admin_user = Customer::where('id',1)->first();
		$admin_email = $admin_user->customer_email;
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Order Dispatch Pending email sent to [".$this->to."]", $text);
		dispatch($cmd);
    }
}
