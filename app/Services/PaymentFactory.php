<?php
/**
 * \class	PaymentFactory
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-12-14
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
namespace App\Services;

use Illuminate\Support\Facades\Facade;


/**
 * \brief Payment Factory Facade handles finding and processing of 
 * requests for payment modules and payment options.
 */
class PaymentFactory extends Facade
{


	/**
	 * See config/app.php and check aliases array for entry to this file.
	 *
	 * @return	string
	 */
	protected static function getFacadeAccessor()
	{
		return 'PaymentFactory';
	}



	/**
	 * Get the modules that are active
	 *
	 * @return array
	 */
	public static function getAvailableModules()
	{
		$classes = array_filter( get_declared_classes(),
			function($className)
			{
				return in_array("App\Services\Payments\IPaymentService", class_implements($className)); 
			});

		return( array_map( function($class) { return(new $class); }, $classes) );
	}



	/**
	 * Return an instance of the required module.
	 * - If not found return NULL
	 *
	 * Format is 217 => "App\Services\Payments\COD_Payment"
	 *
	 * @param	string $module_name
	 * @return 	mixed
	 */
	public static function getModuleByName($module_name)
	{
		$classes = array_filter( get_declared_classes(),
				function($className)
				{
					return in_array("App\Services\Payments\IPaymentService", class_implements($className)); 
				}
			);
		foreach($classes as $class_name)
		{
			$parts = explode("\\", $class_name);
			if($parts[0] != "App") continue;
			if($parts[ sizeof($parts)-1] == $module_name)
			{
				return(new $class_name);
			}
		}
		return null;
	}



	/**
	 * Foreach module, ask the module for payment options.
	 * - If no options available, skip to the next module.
	 * - Options returned are an array of objects with the payment data to return to the web page.
	 *
	 *
	 * @param	array	$modules
	 * @param	mixed	$store
	 * @param	mixed	$user
	 * @param	array	$products
	 * @param	mixed	$address
	 * @return	array
	 */
	public static function getPaymentOptions($modules)
	{
		$options = array();
		foreach($modules as $module)
		{
			$module_options = $module->getHTMLOptions();
			if(!is_null($module_options))
			{
				$options = array_merge($options, $module_options);
			}
		}
		return $options;
	}
}
