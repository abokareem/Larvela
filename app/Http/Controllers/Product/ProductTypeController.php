<?php
/**
 * \class	ProductTypeController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-01-13
 * \version 1.0.2
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
namespace App\Http\Controllers\Product;


use Input;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;


use App\Helpers\StoreHelper;
use App\Services\ProductService;


use App\Jobs\BackInStock;
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
class ProductTypeController extends Controller
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
		$this->setClassName("ProductTypeController");
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
	 * GET ROUTE: /admin/producttype/addnew
	 *
	 * @return	mixed
	 */
	public function Add()
	{
		$this->LogFunction("Add()");
		return view('Admin.ProductTypes.addtype');
	}



	/**
	 * POST ROUTE: /admin/producttype/save
	 *
	 * @return	mixed
	 */
	public function Save(Request $request)
	{
		$this->LogFunction("Save()");
		$o = new ProductType;
		$o->product_type = $request['product_type'];
		$o->product_type_token = $request['product_type_token'];
		if(($rv=$o->save()) > 0)
		{
			$this->LogMsg("Product Type [".$o->product_type."] saved!");
			\Session::flash('flash_message',"Product Type saved!");
		}
		else
		{
			$this->LogError("Save filed!");
			\Session::flash('flash_error',"Product Type save failed!");
		}
		return $this->Show();
	}


	/**
	 * GET ROUTE: /admin/producttype/edit/{id}
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Edit($id)
	{
		$this->LogFunction("Edit()");
		$pt = ProductType::find($id);
		return view('Admin.ProductTypes.edittype',['product_type'=>$pt]);
	}
	


	
	/**
	 * POST ROUTE: /admin/product/update/{id}
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
		return $this->Show();
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
		ProductType::find($id)->delete();
		return $this->Show();
	}
}