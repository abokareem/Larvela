<?php
/**
 * \class	SubscriptionController
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Redirect;
use Input;
use Session;
use App\Http\Requests;
use App\Http\Requests\SubscribeRequest;


use App\Jobs\ConfirmSubscription;
use App\Jobs\SubscriptionConfirmed;

use App\Helpers\StoreHelper;

use App\Models\Store;
use App\Models\Customer;
use App\Models\SubscriptionRequest;
use App\Models\ReSendSubRequest;



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



	public function UnSubscribe($hash)
	{
		$this->LogFunction("UnSubscribe()");
		$this->LogMsg("URL Hash [".$hash."]");

		$SubscriptionRequest = new SubscriptionRequest;

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
					$this->LogMsg("Now Deleted: ".print_r($entry, true));
					$SubscriptionRequest->DeleteRow($entry->id);

					$theme_path = \Config::get('THEME_SUPPORT')."SubscriptionRemoved";
					return view($theme_path);
					##### -----return view('Frontend.SubscriptionRemoved');
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
		#### -----return view('Frontend.SubscriptionError');
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

		$SubscriptionRequest = new SubscriptionRequest;

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
					$cmd = new SubscriptionConfirmed(StoreHelper::StoreData(), $email);
					$this->dispatch( $cmd );
					$theme_path = \Config::get('THEME_SUPPORT')."SubscriptionConfirmed";
					return view($theme_path);
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
