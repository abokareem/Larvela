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
private $return_max_count = 0;



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
	 *
	 *
	 * @param	integer	$state
	 * @param	App/Models/StoreSetting	$setting
	 * @param	array	$results
	 * @return	array
	 */
	public function PreProcessor($state,$setting,$results)
	{
		if($state==0)
		{
			$this->return_max_count = $setting->setting_value;
		}
		return $results;
	}



	/**
	 * Do nothing during the process stage.
	 *
	 * @param	integer	$state
	 * @param	App/Models/StoreSetting	$setting
	 * @param	array	$results
	 * @return	array
	 */
	public function Processor($state,$setting,$results)
	{
		return $results;
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
	public function PostProcessor($state,$setting,$results)
	{
		if(($state==2)&&($this->return_max_count>0))
		{
			do{ array_pop($results); } while(sizeof($results)>$this->return_max_count); # now filter the results
		}
		return $results;
	}
}
