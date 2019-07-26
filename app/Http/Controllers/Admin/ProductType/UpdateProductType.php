<?php
/**
 * \class	UpdateProductType
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
use Redirect;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Bus\Dispatcher;

use App\Models\Store;
use App\Models\ProductType;

use App\Traits\Logger;


/**
 * \brief Update the Product Type. This is an administrator only functions.
 */
class UpdateProductType extends Controller
{
use Logger;


	/**
	 *============================================================
	 * Open log file and check that user is an administrator
	 *============================================================
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("UpdateProductType");
		$this->LogStart();
		$this->middleware(CheckAdmin::class);
	}
	

	/**
	 *============================================================
	 * Close log file
	 *============================================================
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}



	/**
	 *============================================================
	 * POST ROUTE: /admin/product/update/{id}
	 *============================================================
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Update(Request $request, $id)
	{
		$this->LogFunction("Update()");
		if(is_numeric($id))
		{
			if($id == $request['id'])
			{
				$this->LogMsg("Update Product Type [".$request['product_type']."]");
				$o = ProductType::find($id);
				$o->product_type = $request['product_type'];
				$o->product_type_token = $request['product_type_token'];
				if(($rv=$o->save()) > 0)
				{
					$this->LogMsg("Updated!");
					\Session::flash('flash_message',"Product Type updated!");
				}
				else
				{
					\Session::flash('flash_message',"No update performed!");
					$this->LogMsg("No Update performed!");
				}
			}
		}
		Redirect::to('/dashboard');
	}
}
