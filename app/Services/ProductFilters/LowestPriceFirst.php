<?php
/**
 * \class	LowestPriceFirst
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-19
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
class LowestPriceFirst implements IProductOptions
{
use Logger;


/**
 * @var integer	$lowest_price_flag
 */
private $lowest_price_flag = 0;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("LowestPriceFirst");
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
			$this->lowest_price_flag = 1;		
			$values = array_filter($dto->settings->toArray(),
				function($setting)
				{
					if($setting['setting_name'] == "LWEST_PRICE_FIRST") return true;
				});
			if(sizeof($values)>0)
			{
				$this->lowest_price_flag = reset($values)['setting_value'];
				array_push($dto->flags, ["IN_STOCK_ONLY"=>$this->lowest_price_flag]);
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
		if($this->lowest_price_flag	!= 1) return $next($dto);
		foreach($dto->results as $p) { $this->logMsg("BEFORE - ".$p->id." - $".$p->prod_retail_cost); }
		
		usort($dto->results,array($this,"cmp"));
		foreach($dto->results as $p) { $this->logMsg("AFTER - ".$p->id." - $".$p->prod_retail_cost); }
		return $next($dto);
	}



	public function cmp($a,$b)
	{
		return ($a->prod_retail_cost< $b->prod_retail_cost) ? false:true;
	}
}
