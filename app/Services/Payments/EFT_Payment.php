<?php
/**
 * \class	EFT_Payments
 * \date	2019-09-13
 * \author	Sid Young
 * \version	1.0.1
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


use App\Models\StoreSetting;
use App\Services\Payments\IPaymentService;

/**
 * \brief Paypal Module for Express payments using the Javascript API.
 */
class EFT_Payments implements IPaymentService
{


private $MODUEL_CODE = "LARVELA_PAYPAL_EXPRESS";
private $MODUEL_NAME = "Larvela Paypal Express";



	/**
	 *
	 *
	 * @return	string
	 */
	public function getModuleCode()
	{
		return	$this->MODULE_CODE;		
	}



	/**
	 *
	 *
	 * @return	string
	 */
	public function getDisplayName()
	{
		return $this->MODULE_NAME;
	}



	/**
	 *
	 *
	 * @return	boolean
	 */
	public function isActive()
	{
		return true;
	}



	/**
	 *
	 *
	 * @return	boolean
	 */
	public function ProcessPayment($id)
	{
		return true;
	}


	/**
	 *
	 *
	 * @return	array
	 */
	public function getHTMLOptions()
	{
		$options = array();
		$otpion = new \stdClass;
		$option->id = 1;
		$option->display = "EFT Payment";
		$option->html = "<input type='radio' name='".$this->MODULE_CODE."-0' value='".$this->MODULE_CODE."-0>";
		array_push($options,$option);
		return $options;
	}



	public function getPaymentName()
	{
		return "Electronic Funds Transfer";
	}
}
