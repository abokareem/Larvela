<?php
/*!
 * \class	LoginFailed
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-08-02
 *
 * [CC]
 *
 * \addtogroup Security
 * LoginFailed - Send an email to the user that a failed login attempt occured using their email address.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\Mail\LoginFailedEmail;
use App\Jobs\EmailUserJob;


use App\Models\Customer;


/*!
 * \brief  Provide an additonal entry point for when a login attempt to the cart fails.
 */
class LoginFailed implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;


/*!
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/*!
 * The email address to send the email to.
 * @var string $email
 */
protected $email;


    /*!
     *
	 *
     * @param 	mixed	$store	store data collection
     * @param	string	$email	email address of customer
     * @return	void
     */
    public function __construct($store, $email)
    {
		$this->store = $store;
		$this->email = $email;
    }



    /*!
     * Place holder for additional business logic
	 * - email the store Admin that a login failed
	 *
     * @return void
     */
    public function handle()
    {
		$this->EmailCustomer();
		$this->EmailStoreAdmin();
	}


	/*!
	 *
	 *
	 * return void
	 */
	protected function EmailCustomer()
	{
		Mail::to($this->email)->send(new LoginFailedEmail($this->store, $this->email));
	}



	/*!
	 *
	 *
	 * return void
	 */
	protected function EmailStoreAdmin()
	{
		$subject = "[LARVELA] Failed Email Sent to [".$this->email."]";
		$text = "Failed Login attempt for [".$this->email."]. Email sent to Customer. ";
		$from = $this->store->store_sales_email;
		$admin_user = Customer::find(1);
		dispatch(new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
    }
}
