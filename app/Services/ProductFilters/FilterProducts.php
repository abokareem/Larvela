<?php
/**
 * \class	FilterProducts
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-11-13
 * \version	1.0.4
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

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Route;
use Illuminate\Routing\Pipeline;
use App\Http\Requests;



use App\Traits\Logger;


/**
 * \brief Generates the products that will appear on the Store Front page view.
 *
 * {INFO_2018-09-12} "FilterProducts.php" - Removed from Store Front Controller and made into a service.
 */
class FilterProducts
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
		$this->setClassName("FilterProducts");
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
	 * Find all classes that implement the filter interface
	 * Returned in array as: "App\Services\ProductFilters\RandomOrder"
	 *
	 * @return	array
	 */
	public function getFilters()
	{
		$this->LogFunction("getFilters()");

		$classes = get_declared_classes();
		$implements_interface = array();
		foreach($classes as $c)
		{
			$reflect = new \ReflectionClass($c);
			if($reflect->implementsInterface('App\Services\ProductFilters\IProductOptions'))
			{
				$implements_interface[] = new $c;
			}
		}
		return $implements_interface;
	}




	/**
	 * Generate a range of products for the current
	 * store that meet the required (selectable) criterior.
	 *
	 * @return	array
	 */
	public function ReturnProducts()
	{
		$this->LogFunction("ReturnProducts()");

		$filters = $this->getFilters();
		$dto = new FilterDTO;
		$preprocessing = app(Pipeline::class)
			->send($dto)
			->through($filters)
			->via("PreProcessor")
			->then(function($dto){$this->LogMsg("State 0 done");});
		$dto->state++;
		$preprocessing = app(Pipeline::class)
			->send($dto)
			->through($filters)
			->via("Processor")
			->then(function($dto) { $this->LogMsg("State 1 done"); });
		$dto->state++;
		$preprocessing = app(Pipeline::class)
			->send($dto)
			->through($filters)
			->via("PostProcessor")
			->then(function($dto) { $this->LogMsg("State 2 done"); });
		return $dto->results;
	}
}
