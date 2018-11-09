<?php
/**
 * \class	AttributeService
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-10
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


use App\Models\Attribute;
use App\Models\AttributeProduct;


/**
 * \brief Service layer class for the Attribute model
 */
class AttributeService
{
	/**
	 * Given a list of attributes, assign the product to them.
	 *
	 * @pre		Product should not already be assigned to same attribute
	 *
	 * @param	array	$attributes
	 * @param	integer	$product_id
	 * @return	void
	 */
	public static function AssignAttributes($attributes, $product_id)
	{
		$order = 1;
		if(sizeof($attributes)>0)
		{
			foreach($attributes as $c)
			{
				$o = new AttributeProduct;
				$o->product_id = $product_id;
				$o->attribute_id = $a;
				$o->combine_order = $order++;
				$o->save();
			}
		}
	}
}
