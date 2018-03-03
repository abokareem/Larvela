<?php
/**
 * \class	PaymentController
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-09-25
 *
 *
 * Development code to do Paypal
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Requests;
use Input;
use Auth;
##use Collective\Bus\Dispatcher;

use App\Cart;
use App\CartItem;


use Omnipay;
use Omnipay\PayPal;
use Omnipay\Common\Item;
use Omnipay\Common\CreditCard;


use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\ExecutePayment;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;


/** 
 * \brief Cart payment handling business logic.
 *
 * The Payment Controller manages all aspects of the cart including adding items and showing the cart contents.
 *
 * @todo Process INC and DEC on cart
 * @todo Add TAX support
 * @todo Add SHIPPING support
 *
 *
 * 2017-07-12  See: https://developer.paypal.com/docs/integration/direct/express-checkout/integration-jsv4/add-paypal-button/
 *
 * Paypal express checkout has changed, need to review and integrate.
 *
 *
 *
 */
class PaymentController extends Controller
{
/**
 * Paypal client id - obtain from developer portal
 *
 * @var	string	$clientId
 */
protected $clientId = 'AUirUBG6ImLCuDCocB3FXbU9ufAsW3xSQlCxgx0T-fthAPl4_o8t4CVqas5iP-5DuX3Vxbt3V88FiXCi';

/**
 * secret token from paypal developer portal
 *
 * @var	string	$secret
 */
protected $secret   = 'EGsLqUTwuHaZe_ZjnsV-R5y-FA_P4gcgluoUFwDFMn6DVPgal7UoLZ4SrbU6Bg-kdsZf_mYKSv4ooA1u';


	/**
	 * CC Payemnt method - testing only at this stage
	 *
	 * @return	void
	 */
	public function CCPayment()
	{
		$apiContext = new \PayPal\Rest\ApiContext( new \PayPal\Auth\OAuthTokenCredential( $this->clientId, $this->secret ));
		$apiContext->setConfig(array('mode'=>'sandbox'));

		$card = new \PayPal\Api\CreditCard(array(
				'first_name'=>'Example',
				'last_name'=>'User',
				'type'=>'visa',
				'number'=>'4012888888881881',
				'expire_month'=>'01',
				'expire_year'=>'2020',
				'cvv2'=>'123')
			);
#
# pure debug log only, remove when code working and implement transactional loggin
#
		$apiContext->setConfig( array( 'log.LogEnabled' => true, 'log.FileName' => '/logs/cart/paypal.log', 'log.LogLevel' => 'DEBUG'));

		$address = new \PayPal\Api\Address();
		$address->setLine1("3909 Witmer Road")
		    ->setLine2("Niagara Falls")
			    ->setCity("Niagara Falls")
				    ->setState("NY")
					    ->setPostalCode("14305")
						    ->setCountryCode("US")
							    ->setPhone("716-298-1822");
		$fi = new \PayPal\Api\FundingInstrument();
		$fi->setCreditCard($card);

		$payer = new \PayPal\Api\Payer();
		$payer->setPaymentMethod("credit_card")->setFundingInstruments(array($fi));

		$amount = new \PayPal\Api\Amount();
		$amount->setCurrency("AUD")->setTotal("42.45");

		$transaction = new \PayPal\Api\Transaction();
		$transaction->setAmount($amount)->setDescription("SDK Payment attempt.");

		$payment = new \PayPal\Api\Payment();
		$payment->setIntent("authorize")->setPayer($payer)->setTransactions(array($transaction));

		$request = clone $payment;

		try
		{
			$payment->create($apiContext);
		} catch (Exception $ex)
		{
			dd($ex);
		}
		$payId = $payment->getId();
		echo "$payment->getId() = ".$payId;

		$transactions = $payment->getTransactions();
		$relatedResources = $transactions[0]->getRelatedResources();
		$authorization = $relatedResources[0]->getAuthorization();

		$this->SavePaymentData($transactions);
		dd($transactions);
	}




	/**
	 * Implement Paypal REST interface to do CC payment
	 *
	 * 2016-09-28 - WORKING!!!!!!!!!!!!!!!!!
	 *
	 * Does not have email or persons name only CC Name
	 *
	 * @return	void
	 */
	public function xxxCCPayment()
	{
		$gatewayFactory = new \Omnipay\Common\GatewayFactory;
		$gateway = $gatewayFactory->create('PayPal_Rest');
		$gateway->initialize(array(
			'clientId'=>'AUirUBG6ImLCuDCocB3FXbU9ufAsW3xSQlCxgx0T-fthAPl4_o8t4CVqas5iP-5DuX3Vxbt3V88FiXCi',
			'secret'=>'EGsLqUTwuHaZe_ZjnsV-R5y-FA_P4gcgluoUFwDFMn6DVPgal7UoLZ4SrbU6Bg-kdsZf_mYKSv4ooA1u',
			'username'=>'sales-facilitator_api1.hs-retro-fashions.com',
			'password'=>'25TUF4TKHC6BU8SN',
			'signature'=>'AtE7KG872.1TFBSGEyExVr96-Jn6Ala.fmx2fejprABtpsUZB..5wXPn',
			'testMode' => true, // Or false when you are ready for live transactions
			));

		$items = new \Omnipay\Common\ItemBag();
		$items->add(array( 'name' => 'pc-dl-65-red', 'quantity' => '1', 'price' => 65.00,));
		$items->add(array( 'name' => 'pp-n20-sm-red', 'quantity' => '1', 'price' => 19.95,));

		#
		# 2016-09-27 - added a card
		#
		$card = new CreditCard(array(
				'firstName'=>'Example',
				'lastName'=>'User',
				'number'=>'4012888888881881',
				'expiryMonth'=>'01',
				'expiryYear'=>'2020',
				'cvv'=>'123',
				'billingAddress1'=>'1 Scrubby Creek Road',
				'billingCity'=>'Scrubby Creek',
				'billingPostcode'=>'4999',
				'billingState'=>'QLD',
				'billingCountry'=>'AU',

				'email'=>'sid.young@gmail.com',
				'shippingTitle'=>'Shipping Title here',
				'shippingName'=>'AUSPOST',
				'shippingFirstName'=>'Sid',
				'shippingLastName'=>'Young',
				'shippingCompany'=>'Australia Post LPO',
				'shippingAddress1'=>'19 Narambi Street',
				'shippingAddress2'=>'',
				'shippingCity'=>'Ashgrove',
				'shippingPostcode'=>'4060',
				'shippingState'=>'QLD',
				'shippingCountry'=>'AU',
				'shippingPhone'=>'0458396300'
				));
		echo "Create Card <br>";
		$transaction = $gateway->createCard(array('card'=>$card));
		echo "Send Card <br>";
		$response = $transaction->send();
		$data = $response->getData();
		echo "Save Data <br>";
		$this->SavePaymentData($data);

		$transaction_id = $response->getTransactionReference();
		echo "Transaction Reference: [".$transaction_id."] <br>";

		if($gateway->supportsAuthorize())
		{
			echo "Purchase using Card <br>";
			$response = $gateway->purchase(array(
				'returnUrl' => 'http://rockabillydames.com/successful-payment',
				'cancelUrl' => 'http://rockabillydames.com/cancel-payment',
				'amount' => '84.95',
				'currency' => 'AUD',
				'description' => 'Test Purchase using CC- authorise',
				'noShipping'=>0,
				'email'=>'sid@conetix.com.au',
				'card'=>$card
				))->setItems($items)->send();
			$data = $response->getData();
			echo "Save Data <br>";
			$this->SavePaymentData($data);
		}
		else
		{
			echo "Gateway does not support authorize(); <br>";
		}

		if($response->isRedirect())
		{
			$response->redirect();
		}
		

		if($response->isSuccessful())
		{
			echo "Purchase transaction was successful!\n";
			$sale_id = $response->getTransactionReference();
			echo "Transaction reference = " . $sale_id . "\n";
		}
		else
		{
			dd($response);
		}
		return;
	}




	/**
	 * Express Payment without Credit Card
	 *
	 * Notes: need to store the token with the cart id so the success and cancel calls can match the order status.
	 *
	 */
	public function RequestPayment()
	{

		$gatewayFactory = new \Omnipay\Common\GatewayFactory;
		$gateway = $gatewayFactory->create('PayPal_Express');
		$gateway->initialize(array(
			'username'=>'sales-facilitator_api1.hs-retro-fashions.com',
			'password'=>'25TUF4TKHC6BU8SN',
			'signature'=>'AtE7KG872.1TFBSGEyExVr96-Jn6Ala.fmx2fejprABtpsUZB..5wXPn',
			'testMode' => true, // Or false when you are ready for live transactions
			));

		$parameters = $gateway->getParameters();
		# dump the gateway array data...  dd($parameters);

		$items = new \Omnipay\Common\ItemBag();
		$items->add(array( 'name' => 'prova', 'quantity' => '1', 'price' => 40.00,));
		$items->add(array( 'name' => 'prova 2', 'quantity' => '1', 'price' => 10.00,));

		$response = $gateway->authorize(array(
			'returnUrl' => 'http://rockabillydames.com/successful-payment',
			'cancelUrl' => 'http://rockabillydames.com/cancel-payment',
			'amount' => '50.00',
			'currency' => 'AUD',
			'description' => 'This is a test CC authorize transaction.',
		))->setItems($items)->send();
		if($response->isRedirect())
		{
			$response->redirect();
		}
		else
		{
			dd($response);
		}
		return;
	}



	/** 
	 * Called from Paypal
	 * Uses a GET call so token is returned in array $data
	 *
	 * Need to match token to order to then set a tracking status and proceed from there.
	 */
	public function CancelPayment()
	{
		echo "Cancel called! <br><br>";
		$data = \Input::all();
		dd($data);
	}




	/** 
	 * Called from Paypal
	 *
	 * Need to match token to order to then set a tracking status and proceed from there.
	 */
	public function SuccessfullPayment()
	{
		echo "Success! <br><br>";
		$gatewayFactory = new \Omnipay\Common\GatewayFactory;
		$gateway = $gatewayFactory->create('PayPal_Express');
		$gateway->initialize(array(
			'username'=>'sales-facilitator_api1.hs-retro-fashions.com',
			'password'=>'25TUF4TKHC6BU8SN',
			'signature'=>'AtE7KG872.1TFBSGEyExVr96-Jn6Ala.fmx2fejprABtpsUZB..5wXPn',
			'testMode' => true, // Or false when you are ready for live transactions
			));
		$data = \Input::all();

		$this->SavePaymentData($data);
	

		#----------------------------------------
		# returns Success! 
		#
		#array:2 [▼
		#  "token" => "EC-3DR47295U2757760U"
		#    "PayerID" => "SRXW2M35DGUQC"
		#	]
		##

		$purchaseId = $data['PayerID'];
		$transaction_id = $data['token'];

		$response = $gateway->completePurchase([
			'transactionReference'=>$transaction_id,
			'amount' => '50.00',
			'currency' => 'AUD',
			])->send();
		dd($response);
	}
	


	

	/**
	 * Save the array to disk in /logs/cart/YYYY-MM-DD/paypal-tx-YYYY-MM-DD-HHMMSS-0.data
	 *
	 * @param	array	$data
	 * @return	void
	 */
	public function SavePaymentData($data)
	{
		$inc = 0;
		do
		{
			$today = date("Y-m-d");
			$now   = date("his");
			$logdir  = "/logs/cart/".$today;
			if(!is_dir($logdir))
			{
				mkdir( $logdir, 0777, true);
			}
			$today = date("Y-m-d");
			$now   = date("his");
			$file = $logdir."/paypal-tx-".$today."-".$now."-".$inc.".data";
			$inc++;
		} while ( file_exists($file) );
		file_put_contents($file, print_r($data,true));
	}
}
