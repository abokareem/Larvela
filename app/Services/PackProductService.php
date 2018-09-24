<?php
/**
 * \class	PackProductService
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

use App\Models\Attribute;
use App\Models\AttributeProduct;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreSetting;
use App\Services\ImageService;
use App\Traits\Logger;




/**
 * \brief Pack Product Service for handling Controller requests for this type of Product.
 * - Created via the ProductPageFactory service.
 * - Pack instanciatation tested OK.
 */
class PackProductService implements IProduct
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
		$this->setClassName("PackProductService");

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
		return "packproduct";
	}


	# @todo devise the variables needed for a page display of a basic product.
	#
	public function getPageVariables()
	{
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$attributes = Attribute::where('store_id',$store->id)->get();
		$attribute_values = AttributeValue::orderBy('attr_id')->orderBy('attr_sort_index')->get();
		$product_attributes = $this->product->attributes;
		$categories = Category::where('category_store_id',$store->id)->get();
		$images = ImageService::getParentImages($this->product);
		$thumbnails = ImageService::getParentImages($this->product);
		return array(
			'store'=>$store,
			'settings'=>$settings,
			'categories'=>$categories,
			'attributes'=>$attributes,
			'attribute_values'=>$attribute_values,
			'product_attributes'=>$product_attributes,
			'images'=>$images,
			'thumbnails'=>$thumbnails,
			'product'=>$this->product
		);
	}
}
