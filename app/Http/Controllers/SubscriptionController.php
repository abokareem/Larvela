<?php
/**
 * \class	SubscriptionController
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 * [CC]
 *
 * \addtogroup Subscription
 * SubscriptionController - Process the subscribe and unsubscribe business logic.
 * - Dispatches a ConfirmSubscription Job on a subscribe request and
 * - sends a SubscriptionConfirmed confirmed email via the SubscriptionConfirmed Job.
 */
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Redirect;
use Input;
use Hash;
use Session;
use App\Http\Requests;
use App\Http\Requests\SubscribeRequest;


use App\Jobs\ConfirmSubscription;
use App\Jobs\SubscriptionConfirmed;
use App\Jobs\ReSendSubRequest;


use App\Mail\ConfirmSubscriptionEmail;
use App\Mail\SubscriptionConfirmedEmail;


use App\Helpers\StoreHelper;


use App\Models\Store;
use App\Models\Customer;
use App\Models\SubscriptionRequest;



use App\Traits\Logger;

/**
 * \brief MVC Controller for managing subscriptions.
 */
class SubscriptionController extends Controller
{
use Logger;


	/**
	 * Open log file
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->setFileName("store");
	}



	/**
	 * Unsubscribe the customer given a "hash" value which contains
	 * 2 parts, the first is the subscription row ID and the second 
	 * is the hash of the email+security key.
	 *
	 * {FIX_2018-03-07} Added HASH check and more logging.
	 *
	 * @param	string	$hash
	 * @return	mixed
	 */
	public function UnSubscribe($hash)
	{
		$this->LogFunction("UnSubscribe()");
		$this->LogMsg("URL Hash [".$hash."]");
		$store = app('store');
		# format:
		# SRID-HASH (of email);
		#
		$items = explode("-", $hash);
		$this->LogMsg("Items: ".print_r($items, true));
		if(is_numeric($items[0]) && sizeof($items)==2)
		{
			$this->LogMsg("Fetch subscription row ID [".$items[0]."]");
			if($items[0]>0)
			{
				$entry = SubscriptionRequest::find($items[0]);
				if(!is_null($entry))
				{
					$this->LogMsg("Subcription fetched: ".print_r($entry, true));
					$email = $entry->sr_email;
					$hash_value = hash('ripemd160', $email.$store->store_env_code);
					$this->LogMsg("Hash Recevied   [".$items[1]."]");
					$this->LogMsg("Hash calculated [".$hash_value."]");
					if($items[1] == $hash_value)
					{
						$this->LogMsg("Now Deleted: ".print_r($entry, true));
						#
						# @todo Add a Job to process Pre Subscription Delete logic
						#
						SubscriptionRequest::find($entry->id)->delete();
						#
						# @todo Add a Job to process Post Subscription Removed logic
						#

						$theme_path = \Config::get('THEME_SUPPORT')."SubscriptionRemoved";
						return view($theme_path);
					}
					else
					{
						$this->LogMsg("ERROR - Hash value does not match!");
					}
				}
				else
				{
					$this->LogMsg("Subscription not found using ID [".$items[0]."]");
				}
			}
			else
			{
				$this->LogMsg("Customer ID is zero - no fetch - not confirmed");
				return redirect('/');
			}
		}
		$theme_path = \Config::get('THEME_ERRORS')."SubscriptionError";
		return view($theme_path);
	}



	/**
	 * Dispatch a confirmation email job for the current user for this store.
	 *
	 * GET ROUTE: /confirmed/{guid}
	 *
	 * @param	string	$hash
	 * @return	mixed
	 */	
	public function ProcessConfirmed($hash)
	{
		$this->LogFunction("ProcessConfirmed()");
		$this->LogMsg("URL Hash [".$hash."]");
		$store = app('store');
		# format:
		# SRID-HASH (of email);
		#
		$items = explode("-", $hash);
		$this->LogMsg("Items: ".print_r($items, true));
		if(is_numeric($items[0]) && sizeof($items)==2)
		{
			$this->LogMsg("Fetch subscription row ID [".$items[0]."]");
			if($items[0]>0)
			{
				$entry = SubscriptionRequest::find($items[0]);
				if(!is_null($entry))
				{
					$this->LogMsg("Subcription fetched: ".print_r($entry, true));
					$email = $entry->sr_email;
					$hash_value = hash('ripemd160', $email.$store->store_env_code);
					if($items[1] == $hash_value)
					{
						$cmd = new SubscriptionConfirmed($store, $email);
						$this->dispatch( $cmd );
						#
						#
						#
						Mail::to($email)->send(new SubscriptionConfirmedEmail($store, $email));

						$theme_path = \Config::get('THEME_SUPPORT')."SubscriptionConfirmed";
						return view($theme_path);
					}
					else
					{
						$this->LogMsg("ERROR - Hash values do not match!");
					}
				}
				else
				{
					$this->LogMsg("Subscription Entry is missing, may have been unsubscribed and re-subscribed?");
				}
			}
			else
			{
				$this->LogMsg("Customer ID is zero - no fetch - not confirmed");
				return redirect('/');
			}
		}
		$theme_path = \Config::get('THEME_ERRORS')."SubscriptionError";
		return view($theme_path);
	}



	/**
	 * Dispatch a confirmation email job for the current user for this store.
	 * Set the capture session variable to 'done'
	 *
	 * POST ROUTE: /subscribe
	 *
	 * @param	App\Http\Requests\SubscribeRequest $request
	 * @return	mixed
	 */	
	public function AddNewSubscription( SubscribeRequest $request )
	{
		$this->LogFunction("AddNewSubscription()");
		$captcha = $request['g-recaptcha-response'];
		if(strlen($captcha)>0)
		{

			#$secretKey = "Put your secret key here";
			#$ip = $_SERVER['REMOTE_ADDR'];
			#$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
			#$responseKeys = json_decode($response,true);
			#if(intval($responseKeys["success"]) !== 1)
			#{
			#echo '<h2>You are spammer ! Get the @$%K out</h2>';
			#}
			#else
			#{
			#echo '<h2>Thanks for posting comment.</h2>';
			#}

			$email = $request['email'];
			$this->LogMsg("Capture present - processing eMail [".$email."]");
			$this->LogMsg("Dispatch ConfirmSubscription Job");
			$cmd = new ConfirmSubscription(StoreHelper::StoreData(), $email);
			$this->dispatch( $cmd );
			$request->session()->put('capture', 'done');
		}
		else
		{
			$this->LogMsg("Failed validation");
		}
		return Redirect::to("/");
	}
}
