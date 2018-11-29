<?php
/**
 * \class	SendWelcomeEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-08-07
 *
 * [CC]
 *
 * \addtogroup WelcomeProgram
 * SendWelcomeEmail - Sends a welcome email after they have subscribed to the web site or registered.
 */
namespace App\Mail;


use Hash;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use App\Models\Customer;
use App\Models\Store;

use App\Traits\Logger;


/**
 * \brief Send a templated email welcoming the Customer.
 *
 * Called when MyAccount used and a Customer record is inserted. This implies they have registered
 * on the site but not necessarily subscribed.
 */
class SendWelcomeEmail extends Mailable
{
use Queueable, SerializesModels;
use Logger;


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
 * The has used in unsubscribing
 * @var string $hash
 */
public $hash;


/**
 * The filename/template to use.
 * @var string $template
 */
protected $template;


public $ACTION="welcome_customer";



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
     * return the template for emailing
     * @return void
     */
    public function build()
    {
		$from = $this->store->store_sales_email;
		$subject = "Welcome to ".$this->store->store_name;
		return $this->from($from, $this->store->store_name)->subject($subject)->view($this->template);
    }
}
