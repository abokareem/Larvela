<?php
/**
 * \class	RandomOrder
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-13
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
 * \brief If set, limits the number of products returned.
 */
class RandomOrder implements IProductOptions
{
use Logger;


/**
 * @var integer	$return_random
 */
private $return_random = 0;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("RandomOrder");
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
	 * @param	App/Models/StoreSetting	$setting
	 * @return	array
	 */
	public function PreProcessor($dto, Closure $next)
	{
		$this->LogFunction("PreProcessor(".$dto->state.")");
		if($dto->state==0)
		{
			$this->return_random = array_filter($dto->settings->toArray(),
				function($setting)
				{
					if($setting['setting_name'] == "RANDOM_ORDER")
					{
						if($setting['setting_value']==1)
							return true;
					}
				});
			array_push($dto->flags,["RANDOM_ORDER"=>1]);
		}
		return $next($dto);
	}



	/**
	 * Select products in random order using the product_id_list as our range.
	 * Scan through product_rows to find item by ID as its NOT in order.
	 *
	 * @param	integer	$state
	 * @param	App/Models/StoreSetting	$setting
	 * @param	array	$results
	 * @return	array
	 */
	public function Processor($dto, Closure $next)
	{
		if($this->return_random	!= 1) return $next($dto);
		if(sizeof($dto->results) == 0) return $next($dto);
		$pids = array_map(function($product) {return $product['id'];}, $results);

		$selected = array();
		$low_id = 0;
		{
			$rand_idx = mt_rand($low_id, $high_id);
			$pid = $pids[ $rand_idx ];
			if(!in_array($pid, $selected))
			{
				$this->LogMsg("Product ID selected [ $pid ]");
				$row = $this->ProductFinder( $product_rows, $pid );

				if(sizeof($cat_ids)>0) 
				{
					$row->category = Category::find($cat_ids[0]->category_id)->category_title;
				}
				else
					$row->category = "unknown";
				array_push($selected, $pid);
				array_push($products, $row);
			}
		} while (sizeof($selected) != sizeof($product_id_list));
		return $next($dto);
	}
	
	
	
	/**
	 * Set the number of products we want to return in POST Processing
	 *
	 *
	 * @param	integer	$state
	 * @param	App/Models/StoreSetting	$setting
	 * @param	array	$results
	 * @return	array
	 */
	public function PostProcessor($dto, Closure $next)
	{
		return $next($dto);
	}
}
