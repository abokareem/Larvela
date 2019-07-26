<?php
/**
 * \class	DeleteProductType
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2019-07-25
 * \version 1.0.0
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
namespace App\Http\Controllers\Admin\ProductType;


use Input;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;

use App\Http\Middleware\CheckAdmin;

use App\Models\Store;
use App\Models\ProductType;

use App\Traits\Logger;


/**
 * \brief MVC Controller to Handle the Product Type Administration functions.
 */
class DeleteProductType extends Controller
{
use Logger;


	/**
	 * Open log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("DeleteProductType");
		$this->LogStart();
		$this->middleware(CheckAdmin::class);
	}
	
	/**
	 * Close log file
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}






	/**
	 * POST ROUTE: / put route here
	 *
	 * {FIX_2017-10-26} Added method DeleteProductType()
	 *
	 * @param	integer	$id
	 * @return	void
	 */
	public function Delete($id)
	{
		$this->LogFunction("Delete()");

		$count = Product::where('prod_type',$id)->count();
		$this->LogMsg("There are [".$count."] products of type [".$id."].");
		if($count > 0)
		{
			$this->LogError("ERROR - There are products assigned to this type!");
			Redirect::to('/');
		}
		else
		{
			$this->LogMsg("No products assigned to this type");
			ProductType::find($id)->delete();
		}
		Redirect::to('/admin/producttypes');
	}
}
