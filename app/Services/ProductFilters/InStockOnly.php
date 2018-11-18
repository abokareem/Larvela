<?php
/**
 * \class	InStockOnly
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-15
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


/**
 * \brief If set, returns only product that are in stock.
 */
class InStockOnly implements IProductOptions
{
use Logger;


/**
 * @var integer	$in_stock_only
 */
private $in_stock_only = 0;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("InStockOnly");
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
	 *
	 *
	 * @param	integer	$state
	 * @param	App/Models/StoreSetting	$setting
	 * @param	array	$results
	 * @return	array
	 */
	public function PreProcessor($dto, Closure $next)
	{
		$this->LogFunction("PreProcessor(0)");
		if($dto->state==0)
		{
			$this->in_stock_only = 1;		
			$values = array_filter($dto->settings->toArray(),
				function($setting)
				{
					if($setting['setting_name'] == "IN_STOCK_ONLY") return true;
				});
			if(sizeof($values)>0)
			{
				$this->in_stock_only = reset($values)['setting_value'];
				array_push($dto->flags, ["IN_STOCK_ONLY"=>$this->in_stock_only]);
			}
		}

		return $next($dto);
	}



	/**
	 * Select products in random order using the product_id_list as our range.
	 * Scan through product_rows to find item by ID as its NOT in order.
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function Processor($dto, Closure $next)
	{
		$this->LogFunction("Processor(".$dto->state.")");
		return $next($dto);
	}
	
	
	
	/**
	 * Filter the results so only in stock products are returned.
	 * - Depends on if flagged.
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function PostProcessor($dto, Closure $next)
	{
		$this->LogFunction("PostProcessor(".$dto->state.")");
		if($this->in_stock_only	!= 1) return $next($dto);
		
		$this->LogFunction("Before filtering [".sizeof($dto->results)."]");
		$products = array();
		foreach($dto->results as $product)
		{
			if($product->prod_qty > 0) array_push($products, $product);
		}
		$dto->results =  $products;
		$this->LogFunction("After filtering [".sizeof($dto->results)."]");
		return $next($dto);
	}
}