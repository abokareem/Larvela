<?php
/**
 * \class	OrderCancelled
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-09-20
 *
 *
 * [CC]
 * 
 * \addtogroup Transactional
 * OrderCancelled - An order has been cancelled, send an email.
 */
namespace App\Jobs;


use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Models\Store;
use App\Models\Customer;
use App\Models\Order;

use App\Jobs\EmailUserJob;
use App\Traits\TemplateTrait;




/**
 * \brief Send a templated email to customer informing them the their order has been cancelled.
 * {INFO_2017-10-28} Moved template to ./store_env_code/...
 */
class OrderCancelled extends Job
{
use TemplateTrait;



/**
 * The store object from the database tables stores
 * @var mixed $store
 */
protected $store;


/**
 * The order object from the database table "orders"
 * @var mixed $order
 */
protected $order;


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


private $ACTION="ORDER_CANCELLED";


    /**
     * Create a new job instance initialize mail transport and
	 * save store and email details away.
     * Also fetch relevant template using "$ACTION" 
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
		$this->subject = "Order Cancelled";
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
	 * {FIX_2017-11-05} - OrderCancelled.php - removed call to StrReplace
	 *     
     * @return void
     */
    public function handle()
    {
		$Customer = new Customer;
		$Store = new Store;
		$Order = new Order;
		$customer = Customer::find($this->order->order_cid);

		$reason = "not specified - contact store";
		if($this->order->order_status == "C")
		{
			$reason = "Manually Cancelled - contact store for details";
		}
		if(($this->order->order_status == "W") && ($this->order->order_payment_status="W"))
		{
			$reason = "Payment not received in allotted time";
		}

		$path = base_path();
		$file = $path."/templates/".$this->store->store_env_code."/".$this->template_file_name;
		$text = "<h1>WARNING</h1><p>Order Cancelled email NOT sent to: ".$this->to."</p><br/><br/>Check Template file! - <b>".$file."</b>";
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
			$t2 = $Customer->translate($t1, $customer);
			$t3 = $Order->translate($t1, $this->order);
			$text = $Store->translate($t2, $this->store);
		
			$cmd = new EmailUserJob($this->to, $this->from, $this->subject, $text);
			dispatch($cmd);
		}

		$admin_user = Customer::find(1);
		$admin_email = $admin_user->customer_email;
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Order Cancelled message sent to [".$this->to."]", $text);
		dispatch($cmd);
    }
}
