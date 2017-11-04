<?php
/**
 * \class	OrderPlaced
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-22
 *
 *
 * [CC]
 * 
 * \addtogroup Transactional
 * OrderPlaced - The customer has placed an order, send an email, payment may not yet have been made.
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
 * \brief Send a templated email confirming placement of the order in the system.
 *
 * Order will still need to be picked and dispatched. Other jobs will send
 * additional status emails until order is "completed".
 *
 * Requests "ORDER_PLACED" template from template_mapping table.
 * {INFO_2017-10-28} Moved template to ./store_env_code/...
 */
class OrderPlaced extends Job
{
use TemplateTrait;



/**
 * The store object from the database tables stores, has all the details about the selected store.
 * @var mixed $store
 */
protected $store;


/**
 * The order object fromt eh database table "orders"
 * @var mixed $order
 */
protected $order;


/**
 * The email address to send the Subscription Confirmation to.
 * @var string $to
 */
protected $to;


/**
 * Email address to send from.
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


private $ACTION="ORDER_PLACED";


    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using CONFIRM_SUBSCRIPTION action 
	 *
     * @param  mixed	$store	Store data (object)
     * @param  string	$email 	email address of customer
     * @return void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->order = $order;

		$this->to = $email;
		$this->subject = "Order Placed";
		$this->from = $this->store->store_sales_email;

		$this->template_file_name = $this->getTemplate($this->ACTION);
		if(strlen($this->template_file_name)==0)
		{
			$this->template_file_name = strtolower($this->ACTION).".email";
		}
    }



    /**
     * Fetch the template, parse with the store helper and
	 * then send using Swift Mailer. 
	 *
	 * {FIX_2017-11-05} - OrderPlaced.php - removed call to StrReplace
	 *
     * @return void
     */
    public function handle()
    {
		$Store = new Store;
		$Customer = new Customer;

		$path = base_path();
		$file = $path."/templates/".$this->store->store_env_code."/".$this->template_file_name;

		$text = "<h1>WARNING</h1><p>Order Placed email NOT sent to: ".$this->to."</p><br/><br/>Check Template file! - <b>".$file."</b>";
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
			$text = = $Store->translate($t1, $this->store);
		
			$cmd = new EmailUserJob($this->to, $this->from, $this->subject, $text);
			dispatch($cmd);
		}

		$admin_user = Customer::where('id',1)->first();
		$admin_email = $admin_user->customer_email;
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Order #".$this->order->id." placed -> message sent to [".$this->to."]", $text);
		dispatch($cmd);
    }
}
