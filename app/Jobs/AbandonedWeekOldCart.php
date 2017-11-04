<?php
/**
 * \class	AbandonedWeekOldCart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-08-30
 *
 *
 *
 * \addtogroup	CartAbandonment
 * AbandonedWeekOldCart - The cart has been abandond for a week, so email user to entice them back to the store.
 */
namespace App\Jobs;


use Hash;
use App\Jobs\Job;
use App\Helpers\SEOHelper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;


use App\Models\Customer;
use App\Models\Users;
use App\Models\Store;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;


use App\Traits\TemplateTrait;
use App\Traits\Logger;


/**
 * \brief Send a templated email after the cart has been abandoned for a week
 * {INFO_2017-10-28} Moved template to ./store_env_code/...
 * {INFO_2017-11-05} Added $from class var
 */
class AbandonedWeekOldCart extends Job 
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
protected $to;


/**
 * From address for this email
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


private $ACTION="CART_7DAY_ABANDONED";



    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using CONFIRM_SUBSCRIPTION action 
	 *
     * @param  $store     mixed - store data collection
     * @param  $email     string - email address of customer
     * @param  $hash_url  string - hashed URL string to substitue in.
     * @return void
     */
    public function __construct($email, $cart, $store)
    {
		$this->to = $email;
		$this->cart = $cart;
		$this->store = $store;
		$this->from = $store->store_sales_email;
		$this->subject = "Cart Abandoned!";
		$this->template_file_name = $this->getTemplate($this->ACTION);
		if(strlen($this->template_file_name)==0)
		{
			$this->template_file_name = strtolower($this->ACTION).".email";
		}
    }



    /**
     * Fetch the template, parse with the store helper and send to customer
	 *
     * @return void
     */
    public function handle()
    {
		$Customer = new Customer;
		$Store = new Store;
		
		$path = base_path();
		$file = $path."/templates/".$this->store->store_env_code."/".$this->template_file_name;
		$text = "<h1>WARNING</h1><p>Abandond Cart email NOT sent to: ".$this->to."</p><br/><br/>Check Template file! - <b>".$file."</b>";
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
		$cmd = new EmailUserJob($admin_email, $this->from, "[LARVELA] Week Old Abandoned cart message sent to [".$this->to."]", $text);
		dispatch($cmd);
    }
}
