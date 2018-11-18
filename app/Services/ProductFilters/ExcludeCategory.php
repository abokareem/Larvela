<?php
/**
 * \class	ExcludeCategory
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
 * \brief If EXCLUDE_CATEGORY is set in store settings, add this to the flags.
 */
class ExcludeCategory implements IProductOptions
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
		$this->setClassName("ExcludeCategory");
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
	 * Record all the excluded categories for this store.
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function PreProcessor($dto, Closure $next)
	{
		$this->LogFunction("PreProcessor(0)");
		if($dto->state==0)
		{
			$excluded_categories = array_filter($dto->settings->toArray(),
				function($setting)
				{
					if($setting['setting_name'] == "EXCLUDE_CATEGORY") return true;
				});
			array_push($dto->flags, ['exclude_categories'=>$excluded_categories]);
		}
		return $next($dto);
	}



	/**
	 * Do nothing in Processing stage
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
	 * Do nothing in post processing
	 *
	 * @param	mixed	$dto
	 * @return	array
	 */
	public function PostProcessor($dto, Closure $next)
	{
		$this->LogFunction("PostProcessor(".$dto->state.")");
		return $next($dto);
	}
}
