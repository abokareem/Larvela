<?php
/**
 * \class	OutOfStockNotify
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-09-18
 * \version	1.0.2
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
 */
namespace App\Http\Controllers\Ajax;


use App\Http\Requests;
use Illuminate\Routing\Route;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;

use Auth;
use Input;
use Session;


use App\Traits\Logger;
use App\Models\Notification;


/**
 * \brief Manages  Out of Stock Notifications via AJAX calls formt he browser.
 */
class OutOfStockNotify extends Controller
{
use Logger;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-admin");
		$this->setClassName("NotificationController");
		$this->LogStart();
	}



	/**
	 * Close off log
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}



	

	/**
	 * Insert into the "notifications" table the stock SKU and the persons email address
	 * Called via an AJAX call from user web page.
	 * and return a JSON string data with status code.
	 *
	 * email address = nf
	 * input field = sku
	 *
	 * POST ROUTE: /notify/outofstock
	 *
	 * @return	string
	 */
	public function OutOfStockNotify(Request $request)
	{
		$this->LogFunction("OutOfStockNotify() - AJAX Call");

		$rid = 0;
		if(Request::ajax())
		{
			$this->LogMsg("Process AJAX call");

			$form_data = Input::all();
			$email_address = $form_data['nf'];
			$sku = $form_data['sku'];

			$this->LogMsg("Captured [".$email_address."]");
			$this->LogMsg("Product: [".$sku."]");

			if(filter_var($email_address, FILTER_VALIDATE_EMAIL))
			{
				##Amqp::publish('subscribe_out_of_stock', "{'email_address':'".$email_address."','sku':'".$sku."'}", ['exchange_type'=>'direct', 'exchange'=>'laravel', 'auto_delete'=>false]);
				try
				{
					$o = new Notification;
					$o->date_created = date("Y-m-d");
					$o->time_created = date("H:i:s");
					$o->product_code = $sku;
					$o->email_address = $email_address;
					$o->save();
					$rid = $o->id;
				}
				catch(\Illuminate\Database\QueryException $ex)
				{
					$this->LogError("DB insert failed - row may already exist!");
				}
				$this->LogMsg("Row ID: [".$rid."]");
				if($rid>0)
				{
					$this->LogMsg("Return AJAX response");
					$data = array('status'=>'OK','S'=>'OK');
					return json_encode($data);
				}
				$this->LogMsg("Insert failed or user is present.");
			}
		}
		$this->LogMsg("return FAIL response.");
		return json_encode(array('S'=>'FAIL', 'status'=>'FAIL'));
	}
}
