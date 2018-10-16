<?php
/**
 * \class	MsgTemplate
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
 * MsgTemplate - Abstract class implementing the Template design pattern.
 */
namespace App\Events\Larvela;

use Config;

/**
 * \brief An abstract template class for crafting and dispatching Business Process Messages.
 */
abstract class MessageTemplate
{

	/**
	 * Get the resultant JSON data from the calling Message Generator
	 * - Dispatch it using the required transport which is configured in cong/app.php
	 * - Add more transport options into the directory as needed, the AppServiceProvider will autoload them in.
	 */
	public final function dispatch()
	{
		$json = $this->processMsg();
		#
		# Dispatch type is in app.php as an array, only add type with a supported class.
		#
		$transport_types = Config::get("app.metrics_transport");
		#
		# Use the Factory to find and dispatch using the selected transport.
		#
		if(is_array($transport_types))
		{
			array_map(function($transport_type) use ($json)
			{
				$dispatcher = DispatcherFactory::build($transport_type);
				$dispatcher->send($json);
			}, $transport_types);
		}
	}

	abstract protected function processMsg();
}
