<?php
/**
 * \class	LoginFailedEmail
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-08-02
 *
 * [CC]
 *
 * \addtogroup Security
 * LoginFailedEmail - Send an email to the user that a failed login attempt occured using their email address.
 */
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Models\Customer;
use App\Models\Store;



/**
 * \brief Send an email warning of a failed login attempt.
 */
class LoginFailedEmail extends Mailable
{
use Queueable, SerializesModels;



/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
public $store;


/**
 * The email address to send the email to.
 * @var string $email
 */
public $email;


/**
 * @var string $hash
 */
public $hash;


/**
 * @var mixed $customer
 */
public $customer;



/**
 * The filename and path for email template to use.
 * Note: Template may also be made of SEOHelper calls.
 * @var string template_file_name
 */
protected $template;


/**
 * The template to use
 * @var string $ACTION
 */
private $ACTION="login_failed";


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
		$this->store = $store;
		$this->email = $email;
		$this->customer = Customer::where('customer_email', $email)->first();
		$this->template = "Mail.".$this->store->store_env_code.".".$this->ACTION;
		$this->hash = $this->customer->id."-".hash('ripemd160', $email.$store->store_env_code);
    }



    /**
	 * Return the blade template
	 *
     * @return mixed
     */
    public function build()
    {
		$subject = $this->store->store_name." - Failed login attempt on your Cart!";
		return $this->from($this->store->store_sales_email,$this->store->store_name)->subject($subject)->view($this->template);
    }
}
