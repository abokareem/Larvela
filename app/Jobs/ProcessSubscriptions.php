<?php
/**
 * \class	ProcessSubscriptions
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-03-10
 * \version	1.0.1
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 * \addtogroup	CRON
 * ProcessSubscription - Run through subscription table and process each waiting record.
 * - Resend  subscription 1 day after, then a final after 3 days, then on the 7th day, remove the record.
 * - At the end of the run, email the store admin with a report.
 * - Also record the number of subscription requests processed into the subscription_stats table.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;
use App\Jobs\ReSendSubRequest;
use App\Jobs\FinalSubRequest;

use App\Models\Store;
use App\Models\Customer;
use App\Models\SubscriptionRequest;
use App\Models\SubscriptionStat;

use App\Traits\Logger;


/**
 * \brief Send a templated email asking for confirmation of the email address and subscription to site.
 */
class ProcessSubscriptions extends Job 
{
use Logger;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
protected $store;


/**
 * The email address to send from
 * @var string $from
 */
protected $from;




    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using CONFIRM_SUBSCRIPTION action 
	 *
     * @param  $store     mixed - store data collection
     * @return void
     */
    public function __construct()
    {
		$store = app('store');
		$this->setFileName("store-jobs");
		$this->LogStart();
		$this->store = $store;
		$this->from = ["$store->store_sales_email"=>"Larvela Subcription Engine"];
    }



    /**
     * Fetch each subscription request and process it.
	 *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("ProcessSubscription::handle()");
		$admin_user = Customer::find(1);
		$admin_email = $admin_user->customer_email;

		$counter_final = 0;
		$counter_resend = 0;
		$counter_deleted = 0;
		$counter_progressing = 0;
		$DAY = 8;

		$emailed = array();
		do
		{
			$DAY--;
			$this->LogMsg("Process for Day [".$DAY."]");
			$requests = SubscriptionRequest::where('sr_status',"W")->where('sr_process_value',$DAY)->get();
			foreach($requests as $request)
			{
				if( ($request->sr_date_created == date("Y-m-d")) ||
					($request->sr_date_updated == date("Y-m-d")) )
				{
					continue;
				}
				switch($DAY)
				{
					case 7:
						$this->LogMsg("Delete Record [".$request->id."]");
						array_push($emailed, "Delete - ".$request->sr_email);
						$request->delete();
						$counter_deleted++;
						break;
					case 6:
					case 5:
					case 4:
					case 2:
					case 0:
						$this->LogMsg("No action for [".$request->id."] - [".$request->sr_email."]");
						$counter_progressing++;
						$request->sr_process_value++;
						$request->sr_date_updated = date("Y-m-d");
						$request->save();
						break;
					case 3:
						$this->LogMsg("Send Final Email Request to [".$request->id."] - [".$request->sr_email."]");
						$counter_final++;
						array_push($emailed, "Final Email - ".$request->sr_email."\n");
						$request->sr_date_updated = date("Y-m-d");
						$request->sr_process_value++;
						$request->save();
						dispatch( new FinalSubRequest($this->store,$request->sr_email));
						break;
					case 1:
						$this->LogMsg("Send Follow up Request to [".$request->id."] - [".$request->sr_email."]");
						$counter_resend++;
						$msg =  "Resend Request - ".$request->sr_email."\n";
						array_push($emailed, $msg);
						$request->sr_process_value++;
						$request->sr_date_updated = date("Y-m-d");
						$request->save();
						dispatch( new ReSendSubRequest($this->store,$request->sr_email));
						break;
				}
			}
		}while ($DAY > 0);
		$this->LogMsg("Done! - Insert DB stats.");

		$today = date("Y-m-d");
		$o = new SubscriptionStat;
		$o->subs_completed = SubscriptionRequest::where('sr_status',"C")->where('sr_date_updated',$today)->count();
		$o->subs_final_count = $counter_final;
		$o->subs_deleted_count = $counter_deleted;
		$o->subs_resent_count = $counter_resend;
		$o->subs_date_created = date("Y-m-d");
		$o->save();
		$this->LogMsg("Done! - Compile and send Report.");
	}



	/**
	 * refactored this into its own function.
	 *
	 * DEPRECATED
	 *
	 * @return	void
	 */
	public function SendReport($counter_final,$counter_deleted,$counter_resend,$counter_progressing)
	{

		$subject = "[Larvela] Subscription Processing for ".date("Y-m-d");

		$file = base_path()."/templates/subscription_processing.tpl";

		$text = "<html><head><title>Subscription Processing Report</title></head><body bgcolour='white'><h2>Todays Subscription Processing</h2>\n";
		if(file_exists($file))
		{
			$t1 = file_get_contents($file);
			$t2 = str_replace("{DELETED}",$counter_deleted, $t1);
			$t3 = str_replace("{FINAL}",$counter_final, $t2);
			$t4 = str_replace("{RESEND}",$counter_resend, $t3);
			$text = str_replace("{INPROGRESS}",$counter_progressing, $t4);
		}
		else
		{
		}
		$text.="<ul>\n";
		foreach($emailed as $e)
		{
			$text .= "<li>".$e."</li>\n";
		}
		$text.="</ul>\n\n";
		$text.="Entries Deleted - ".$counter_deleted."<br>\n";
		$text.="Entries Final Request - ".$counter_final."<br>\n";
		$text.="Entries Resent - ".$counter_resend."<br>\n";
		$text.="Entries in Progress - ".$counter_progressing."<br><br>\n\n";
		$text.="<p>Run Completed</p></body></html>\n";
		dispatch(new EmailUserJob($admin_email, $this->from, $subject, $text));
		$this->LogMsg("Done!");
    }
}
