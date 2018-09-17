<?php
/**
 * \class	VirtualProductService
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
use App\Models\ProductType;


use App\Traits\Logger;



/**
 * \brief Virtual Product Service for handling Controller requests for this type of Product.
 * - Created via the ProductPageFactory service.
 * - Virtual instanciatation tested OK.
 */
class VirtualProductService implements IProduct
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
		$this->setClassName("VirtualProductService");

		$this->product = $product;
	}



	/**
	 * Return the name of the page for display of this type of product.
	 * View will be "Themes/<theme_name>/Product.xxxxxxx.blade.php"
	 * Where xxx is the type of Virtual
	 *
	 * There are two types of Virtual Products, limited and unlimited.
	 * - Unlimited would be eBook sales where you can sell a PDF forever.
	 * - Limited would be Concert sales with a fixed number of seats.
	 * - Virtual products are downloadable and no Physical product is "shipped".
	 *
	 * @return	string
	 */
	public function getPageRoute()
	{
		$types = ProductType::where('product_type','like','Virtual%')->get();
		foreach($types as $type)
		{
		}
		return "packproduct";
	}


	# @todo devise the variables needed for a page display of a basic product.
	#
	public function getPageVariables()
	{
		return array();
	}
}
