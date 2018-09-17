<?php
/**
 * \class	ParentProductService
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-09-17
 * \version	1.0.0
 *
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

use App\Models\Product;


use App\Traits\Logger;



/**
 * \brief Parent Product Service for handling Controller requests for this type of Product.
 * - Created via the ProductPageFactory service.
 * - Parent instanciatation tested OK.
 */
class ParentProductService implements IProduct
{
use Logger;


/**
 * The Product object
 * @var mixed $product
 */
protected $product;



	function __construct($product)
	{
		$this->setFileName("store");
		$this->setClassName("ParentProductService");

		$this->product = $product;
	}



	/**
	 * Return the name of the page for display of this type of product.
	 * View will be "Themes/<theme_name>/Product.basic.blade.php"
	 *
	 * @return	string
	 */
	public function getPageRoute()
	{
		return "parentproduct";
	}


	# @todo devise the variables needed for a page display of a parent product.
	# need to get all the child (basic) products and their stock levels etc.
	#
	public function getPageVariables()
	{
		return array();
	}
}
