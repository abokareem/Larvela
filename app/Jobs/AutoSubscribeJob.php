<?php
/**
 * \class	AutoSubscribeJob
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-07-23
 *
 *
 * \addtogroup Subscription
 * Subscribe the new Customer to the Subscriptions table and
 * provide a place where additional business logic can be called.
 */
namespace App\Jobs;

use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;



use App\Models\SubscriptionRequest;


/**
 * \brief Subscribe the new Customer to the Subscriptions table and
 * provide a place where additional business logic can be called.
 */
class AutoSubscribeJob implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;




    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using CONFIRM_SUBSCRIPTION action 
	 *
     * @param 	mixed	$store	- The Store object.
     * @param	string	$email	- The customer email address.
     * @return	void
     */
    public function __construct($store, $email)
    {
		$this->SubscribeEmail($email);
    }



    /**
	 * Place Holder for additional Business Logic
	 *
     * @return void
     */
    public function handle()
    {
    }





	/**
	 * Subscribe the user to the subscription request table
	 *
	 * @param	string	$email
	 * @return	integer
	 */
	protected function SubscribeEmail( $email )
	{
		$today = date("Y-m-d");
		$o = new SubscriptionRequest;
		$o->sr_email = $email;
		$o->sr_status = "C";
		$o->sr_process_value = 0;
		$o->sr_date_created = date("Y-m-d");
		$o->sr_date_updated= date("Y-m-d");
		$o->save();
		return $o->id; 
	}
}
