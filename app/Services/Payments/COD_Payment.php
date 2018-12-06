<?php
/**
 * \class	COD_Payments
 * \date	2018-10-29
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
 */
namespace App\Services\Payments;


use App\models\StoreSetting;
use App\Services\Payments\IShippingModule;



/**
 * \brief Module for calculating postage costs and options for using AU Post
 */
class COD_Payments implements IPaymentModule
{

private $MODULE_CODE = "LARVELA_COD";



	/**
	 * Return the unique module code <provider>_<shipping_type>
	 *
	 * @return	string
	 */
	public function getModuleCode()
	{
		return $this->MODULE_CODE;
	}



	/**
	 * Return the display name
	 *
	 * @return	string
	 */
	public function getDisplayname()
	{
		return "Cash On Delivery";
	}



	/**
	 * Return an array of displayable options as a radio group named 'payment'
	 * - all modules return this so you can display 1 or more payment options.
	 *
	 * @return	array
	 */
	public function Calculate()
	{
		$options = array();

		$option = new \stdClass;
		$option->id = 1;
		$option->display = "Cash On Delivery";
		$option->html = "<input type='radio' name='payment' value='".$this->MODULE_CODE."-0'>";
		$option->value = $this->MODULE_CODE."-0";
		
		array_push($options, $option);
		return $options;
	}



	/**
	 * Module is always active in this demo code.
	 * @return	boolean
	 */
	public function isActive()
	{
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		foreach($settings as $setting)
		{
			if($s->setting_name=="COD_ENABLED")
			{
				return ($s->setting_value==1) ? true : false;
			}
		}
	}
}