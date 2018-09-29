<?php
/**
 * \class	AddToCartMessage
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-08-12
 * \version 1.0.2
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
 * \addtogroup Messaging
 * AddToCartMessage - Craft a JSON string and return.
 */
namespace App\Events\Larvela;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Store;

use App\Events\Larvela\MessageTemplate;
use App\Traits\Logger;

/**
 * \brief Formulate a JSON message containing data about what has been added to a cart in which store
 */
class AddToCartMessage extends MessageTemplate
{
use Logger;


/**
 * @var mixed $store
 */
protected $store;


/**
 * @var mixed $cart
 */
protected $cart;


/**
 * @var mixed $user
 */
protected $user;


/**
 * @var mixed $product
 */
protected $product;


	/**
	 * Take the data given and save it for processing later.
	 *
	 * @return	void
	 */
	public function __construct($store,$user,$cart,$product)
	{
		$this->setFileName('larvela');
		$this->setClassName('AddToCartMessage');
		$this->store = $store;
		$this->user = $user;
		$this->cart = $cart;
		$this->product = $product;
	}


	/**
	 * Take the data given and format up a JSON response.
	 *
	 * Called via the dispatch method in the Abstract Base Class.
	 * Provides a uniform way to dispatch messages.
	 * @return	string;
	 */
	protected function processMsg()
	{
		$this->LogFunction("processMsg()");
		$this->LogMsg("Store Code [".$this->store->store_env_code."]");
		$msg = array('store_code'=>$this->store->store_env_code);
		#
		# @todo Add code to process the message from the add code dispatch()
		#
		return json_encode($msg);
	}
}
