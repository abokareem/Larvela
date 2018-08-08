<?php
/**
 * \class	ConfirmSubscriptionEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-08-08
 *
 * [CC]
 *
 * \addtogroup	Subscription
 * ConfirmSubscription - A customer has visited the site and entered their email address in a subscription box,
 * we now need to send them a confirmation email with a link.
 */
namespace App\Mail;


use Hash;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use App\Models\Store;
use App\Models\Customer;
use App\Models\SubscriptionRequest;


/**
 * \brief Send a templated email asking for confirmation of the email address and subscription to site.
 */
class ConfirmSubscriptionEmail extends Mailable
{
use Queueable, SerializesModels;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
public $store;




/**
 * The email address to send the Subscription Confirmation to.
 * @var string $email
 */
public $email;


/**
 * A URL string containing the hashed values to be confirmed
 * @var string $hash
 */
public $hash;


/**
 * The Customer we are sending the email to.
 * @var mixed $customer
 */
public $customer;


/**
 * The email template to use.
 * @var string $template
 */
protected $template;

private $ACTION="confirm_subscription";



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
		$this->customer = Customer::where('customer_email', $email)->first();
		$this->template = "Mail.".$this->store->store_env_code.".".$this->ACTION;
		$this->hash = $this->customer->id."-".hash('ripemd160', $email.$store->store_env_code);
    }



    /**
     * Return the Mail template blade
	 *
     * @return mixed
     */
    public function build()
    {
		$subject = $this->store->store_name." - Subscription Confirmation Required";
		return $this->from($this->store->store_sales_email,$this->store->store_name)->subject($subject)->view($this->template);
    }
}
