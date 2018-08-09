<?php
/**
 * @class	MailRunController
 * @date	2018-04-04
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 *
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;

use App\Models\Customer;
use App\Models\Store;
use App\Models\SubscriptionRequest;


use Illuminate\Support\Facades\Mail;
use App\Mail\MailOut;



/**
 * \brief handle the selection of and dispatching of mailrun jobs.
 */
class MailRunController extends Controller
{


	/**
	 * Return a view of the control panel, when getting templates, we only want
	 * those that have @extends() in them
	 *
	 *
	 * GET ROUTE: /admin/mailrun/panel
	 *
	 * @return	mixed
	 */
	public function ShowPanel()
	{
		$store = app('store');
		$templates = array("Select one.....");
		$pattern = "/\bextends\b/i";
		$path = resource_path()."/views/Mail/".$store->store_env_code."/";
		if(is_dir($path))
		{
			$files = scandir($path);
			foreach($files as $f)
			{
				if($f == ".") continue;
				if($f == "..") continue;
				if(!is_dir($f))
				{
					$file = $path.$f;
					$o = preg_grep($pattern, file($file));
					if(sizeof($o) > 0)
					{
						$parts = explode(".",$f);
						array_push($templates, $parts[0]);
					}
				}
			}
		}
		else
		{
			die("Directory path [".$path."]is invalid?");
		}
		$s_count = Customer::where('customer_store_id',$store->id)->count();
		$c_count = SubscriptionRequest::all()->count();
		return view("Admin.MailRun.control",['store'=>$store, 'c_count'=>$c_count, 's_count'=>$s_count, 'templates'=>$templates]);
	}




	public function StartMailRun()
	{
		$store=app('store');
		$path = resource_path()."/views/Mail/".$store->store_env_code."/";
		
		$include_customers = 0;
		$include_subscribers = 0;
		$test_only = 0;
		$template = "";

		$form = \Input::all();
		if(array_key_exists("template",$form)) { $template = $form['template']; }
		if(array_key_exists("cb_test",$form)) { $test_only = 1; }
		if(array_key_exists("cb_customers",$form)) { $include_customers = 1; }
		if(array_key_exists("cb_subscriptions",$form)) { $include_subscribers = 1; }

		if(($template == "Select one.....") ||($template == "select one.....")) return $this->ShowPanel();


		$email = "sid.young@gmail.com";
		#$email = "janelle.blavius@gmail.com";
		$subject = "TEST - Empty handed on Valentines day?";

		$hash = "";
		#
		# 2018-04-04 uses mailable classes
		#
		Mail::to($email)->send(new MailOut($store, $email, $subject, "valentines-day", $hash));




		$filename = "mail-run";
		##### dispatch(new MailRun($store,$email,$subject,$filename));


		dd($this);
	}



}
