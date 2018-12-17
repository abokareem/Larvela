<?php
/**
 * \class	PostPurchaseEmailJob
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-08-30
 *
 * [CC]
 *
 * \addtogroup Post_Purchase
 * PostPurchaseEmail - Send an email at some period after the sale to congratulate customer on their purchase and entice them back.
 * - Could be used to send additional information related to the product or similar products.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\User;
use App\Models\Cart;
use App\Models\Store;
use App\Models\Customer;
use App\Jobs\EmailUserJob;
use App\Mail\PostPurchaseEmail;


use App\Traits\Logger;


/**
 * \brief Send an email to the customer at some time after they have made a purchase and notify the admin its been sent.
 */
class PostPurchaseEmailJob extends Job implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;
use Logger;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;



/**
 * Email address to send to
 * @var string $to
 */
protected $to;

/**
 * Email address to send from.
 * @var string $from
 */
protected $from;

	/**
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
		$this->from = ["$store->store_sales_email"=>"Larvela Post Purchase Engine"];
    }



    /**
	 * Run the task and email the store admin
	 *
     * @return void
     */
    public function handle()
    {
		$this->Run();

		$text = "Post Purchase email sent to Customer [".$this->email."] from Store [".$this->store->store_name."]";
		$admin_user = Customer::find(1);
		dispatch(new EmailUserJob($admin_user->customer_email, $this->from, "[LARVELA] Post Purchase email sent to [".$this->email."]", $text));
    }


    /**
	 * Run the task and email the store admin
	 *
     * @return void
     */
	public function Run()
	{
		Mail::to($this->email)->send(new PostPurchaseEmail($this->store, $this->email, $this->cart));
	}
}
