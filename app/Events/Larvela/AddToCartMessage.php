<?php
/**
 * \class	AddToCartMessage
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-08-12
 * \version 1.0.0
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
 * \addtogroup Messaging
 * AddToCartMessage - Craft a JSON string and return.
 */
namespace App\Events\Larvela;



use App\Events\Larvela\MessageTemplate;


/**
 * \brief Formulate a JSON message containing data about what has been added to a cart in which store
 */
class AddToCartMessage extends MessageTemplate
{
	public function __construct($store,$cart,$user,$product)
	{
		$data = array();
		$this->msg = json_encode($data);
	}


	/**
	 *
	 *
	 */
	protected function processMsg()
	{
		#
		# @todo Add code to process the message from the 
		#
		return $this->msg;
	}
}
