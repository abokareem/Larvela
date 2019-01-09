<?php
/**
 * \class	Paypal_Payments
 * \date	2017-09-18
 * \author	Sid Young
 * \version	1.0.0
 *
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


	public function getHTMLOptions()
	{
		$html = <<< ENDPP
		<option name="LARVELA_PAYPAL_EXPRESS" id="LARVELA_PAYPAL_EXPRESS">
ENDPP;
		return $html;
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
