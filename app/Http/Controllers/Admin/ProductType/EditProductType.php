<?php
/**
 * \class	EditProductType
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


use App\Helpers\StoreHelper;

use App\Jobs\ResizeImages;
use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;


use App\Models\Store;
use App\Models\Image;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductType;
use App\Models\CategoryProduct;


use App\Traits\Logger;


/**
 * \brief MVC Controller to Handle the Product Type Administration functions.
 */
class EditProductType extends Controller
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
		$this->setClassName("EditProductType");
		$this->LogStart();
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
	 * GET ROUTE: /admin/producttypes
	 *
	 *
	 * {FIX_2017-10-24} Changed get all product types to Eloquent call
	 *
	 * @return	void
	 */
	public function Show()
	{
		$this->LogFunction("Show()");
		$pt = ProductType::get();
		return view('Admin.ProductTypes.showtypes',['product_types'=>$pt]);
	}



	/**
	 *============================================================
	 * GET ROUTE: /admin/producttype/edit/{id}
	 *============================================================
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Edit($id)
	{
		$this->LogFunction("Edit()");
		$store = app('store');
		$pt = ProductType::find($id);
		return view('Admin.ProductTypes.edittype',['store'=>$store,'product_type'=>$pt]);
	}
}
