<?php
/**
 * \class	AutoSubscribeJob
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-07-27
 *
 *
 *
 * [CC]
 */
namespace App\Jobs;


use Hash;
use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Models\SubscriptionRequest;



/**
 * \brief When a customer is added, automatically add them to the subscribers table
 */
class AutoSubscribeJob extends Job
{
private $ACTION="AUTO_SUBSCRIBE_JOB";



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
		$this->SubscribeEmail($email);
    }



    /**
	 * Nothing to do here.
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
		$SubscriptionRequest = new SubscriptionRequest;
		$data = array('sr_email'=>$email,'sr_status'=>"C", 'sr_process_value'=>0,
			'sr_date_created'=>$today,
			'sr_date_updated'=>$today
			);
		return $SubscriptionRequest->InsertData( $data );
	}
}
