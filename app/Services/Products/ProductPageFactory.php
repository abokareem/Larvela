<?php
/**
 * \class	ProductPageFactory
 * \date	2018-09-17
 * \author	Sid Young <sid@off-grid-engineering.com>
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
 */
namespace App\Services\Products;

use App\Exceptions\Handler;
use App\Models\ProductType;


/**
 * \brief Return a suitable Object from the factory to send our data to.
 */
class ProductPageFactory 
{

	/**
	 * Instanciate a new Product Object used in the ProductPageController.
	 *
	 * @param	App\Models\Product	$product
	 * @return	mixed
	 */
	public static function build($product)
	{
		$type = ProductType::find($product->prod_type);
		if(!is_null($type))
		{
			$parts = explode(" ",$type->product_type);
			$object_name = trim(ucwords($parts[0]))."ProductService";
			$class_name = "App\\Services\\".$object_name;
			require_once app_path()."/Services/". $object_name.'.php';
			if(class_exists($class_name))
			{
				return new $class_name($product);
			}
			else
			{
				throw new \Exception("Product class not found.");
			}
		}
		else
		{
			throw new \Exception("Invalid type given.");
		}
	}
}
