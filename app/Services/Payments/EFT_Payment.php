<?php
/**
 * @class	EFT_Payments
 * @date	2019-09-13
 * @author	Sid Young
 * @version	1.0.2
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


private $MODULE_CODE = "LARVELA_EFT_PAYMENT";
private $MODULE_NAME = "EFT Payment";



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
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		foreach($settings as $s)
		{
			if($s->setting_name==$this->MODULE_CODE)
			{
				return ($s->setting_value==1) ? true : false;
			}
		}
		return false;
	}


	/**
	 * Construct the HTML and variables for this module.
	 * These are returned to the controller and should be injected into the Payment view
	 *
	 *
	 * @return	mixed
	 */
	public function getPaymentHTML()
	{
		$vars = array('BSB'=>"","AC"=>"",'ACNAME'=>"",'BANKNAME'=>"");
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)
			->where('setting_name','LIKE','LARVELA_EFT_PAYMENT-%')
			->get();
		if($settings->isEmpty())
		{
			return "No EFT details are available";
		}
		foreach($settings as $s)
		{
			$parts = explode("-",$s->setting_name);
			$vars[$parts[1]]=$s->setting_value;
		}
		$html=<<<EXIT

		<table class='table'>
		<tr>
			<td class='eft-acnum'>Account Name</td>
			<td class='eft-acnum-var'>{$vars['ACNAME']}</td>
		</tr>
		<tr>
			<td class='eft-bsb'>BSB</td>
			<td class='eft-bsb-var'>{$vars['BSB']}</td>
		</tr>
		<tr>
			<td class='eft-acnum'>Account Number</td>
			<td class='eft-acnum-var'>{$vars['AC']}</td>
		</tr>
		<tr>
			<td class='eft-acnum'>Financial Institution</td>
			<td class='eft-acnum-var'>{$vars['BANKNAME']}</td>
		</tr>
		</table>
EXIT;
		$view_data = new \stdClass;
		$view_data->vars = $vars;
		$view_data->html = $html;
		$view_data->settings = $settings;
		return $view_data;
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
	 * Return an object that has the details/form data
	 * to display back to the user for selection.
	 *
	 * @return	array
	 */
	public function getHTMLOptions()
	{
		$options = array();
		$option = new \stdClass;
		$option->id = 1;
		$option->display = "EFT Payment";
		$option->name = $this->MODULE_NAME;
		$option->code = $this->MODULE_CODE;
		$option->value = $this->MODULE_CODE."-0";
		$option->html = "<input type='radio' name='payment_option' value='".$this->MODULE_CODE."-0'>";
		array_push($options,$option);
		return $options;
	}



	public function getPaymentName()
	{
		return $this->MODULE_NAME;
	}
}
