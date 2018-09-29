<?php
/**
 * \class	Paypal_Payments
 * \date	2017-09-18
 * \author	Sid Young
 *
 *
 * Payment Gateway for Paypal Payments.
 */
namespace App\Services\Payments;

use App\Services\Payments\PaymentModule;
use App\Traits\Logger;

/**
 * \brief Paypal Module for Express payments using the Javascript API.
 */
class Paypal_Payments implements PaymentModule
{
use Logger;

	public function __construct()
	{
		$this->setFileName("payments");
		$this->setClassName("Paypal_Payments");
		$this->LogStart();
	}


	public function __destruct()
	{
		$this->LogEnd();
	}



	public function getPaymentName()
	{
		$this->LogFunction("getPaymentName()");
		return "Paypal";
	}
	

	public function getPaymentType()
	{
		$this->LogFunction("getPaymentName()");
		return "PP";
	}


	public  function ProcessPayment($cart_id)
	{
	}



	
	public  function extractEmail($data)
	{
	}



	public  function extractName($data)
	{
	}



	public  function extractAddress($data)
	{
	}
	

	public  function extractStatus($data)
	{
	}


	public  function extractItems($data)
	{
	}



}
