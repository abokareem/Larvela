<?php
/**
 * \class	FetchAllProducts
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-17
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
namespace App\Services\ProductFilters;

use Closure;
use App\Traits\Logger;
use App\Models\Product;
use App\Models\Category;
use App\Models\CategoryProduct;


/**
 * \brief Returns all product that are visible.
 */
class FetchProducts implements IProductOptions
{
use Logger;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("FetchProducts");
		$this->LogStart();
	}



	/**
	 * Close off log
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}


	/**
	 * no Pre-processing required so just return
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function PreProcessor($dto, Closure $next)
	{
		$this->LogFunction("PreProcessor(0)");
		return $next($dto);
	}



	/**
	 * Select products using the required flags and categories for this store if
	 * a specific category has not been selected.
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function Processor($dto, Closure $next)
	{
		$this->LogFunction("Processor(".$dto->state.")");

		$store = app('store');
		$categories = array_unique(array_map(
			function($c){return $c['id'];},
			Category::where('category_store_id',$store->id)->get()->toArray()
			),SORT_NUMERIC);
		$pids = array_unique(array_map(
			function($cp){return$cp['product_id'];},
			CategoryProduct::select('product_id')->whereIn('category_id',$categories)->get()->toArray()));
		$dto->results = Product::whereIn('id',$pids)->get();
		return $next($dto);
	}
	
	
	
	/**
	 * Do nothing
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function PostProcessor($dto, Closure $next)
	{
		return $next($dto);
	}
}
