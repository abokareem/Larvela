<?php
/**
 * \class	ProductTypeFactory
 * \date	2018-08-22
 * \author	Sid Young <sid@off-grid-engineering.com>
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
 */
namespace App\Services;

use App\Models\ProductType;

/**
 * \brief Return a suitable Product Type Object
 */
class ProductTypeFactory 
{

	/**
	 * Craft a route string and return it.
	 *
	 * @param	string	$type
	 * @return	mixed
	 */
	public static function BuildRoute($type)
	{
		$product_type = ProductType::find($type);
		$t1_name = strtolower($product_type->product_type);
		$t2_name = trim(str_replace([' product',')'],"",$t1_name));
		$t3_name = trim(str_replace(" (","_",$t2_name));
		$t4_name = trim(str_replace(" ","",$t3_name));
		return "add_".$t4_name;
	}
}
