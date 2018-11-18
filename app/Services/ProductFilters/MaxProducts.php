<?php
/**
 * \class	MaxProducts
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-13
 * \version	1.0.3
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
class MaxProducts implements IProductOptions
{
use Logger;


/**
 * @var integer	$return_max_count
 */
private $max_count = 0;



	/**
	 * Setup logging and test cookie
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("MaxProducts");
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
	 * Extract out the specific store settings and save in flags and class variable
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function PreProcessor($dto, Closure $next)
	{
		$this->LogFunction("PreProcessor(".$dto->state.")");
		if($dto->state==0)
		{
			$this->max_count = 0;
			$values = array_filter($dto->settings->toArray(),
				function($setting)
				{
					if($setting['setting_name'] == "MAX_PRODUCT_FETCH")
					{
						if($setting['setting_value'] > 0)
						{
							return true;
						}
					}
				});
			if(sizeof($values)>0)
			{
				$this->max_count = reset($values)['setting_value'];
				array_push($dto->flags, ["MAX_PRODUCTS"=>$this->max_count]);
			}
		}
		return $next($dto);
	}



	/**
	 * Do nothing during the process stage.
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
	 * Set the number of products we want to return in POST Processing
	 *
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function PostProcessor($dto, Closure $next)
	{
		$this->LogFunction("PostProcessor(".$dto->state.")");
		if(($dto->state==2)&&($this->max_count>0))
		{
			$this->LogMsg("Reducing results to max count of [".$this->max_count."]");
			do{ array_pop($dto->results); } while(sizeof($dto->results)>$this->max_count);
		}
		return $next($dto);
	}
}
