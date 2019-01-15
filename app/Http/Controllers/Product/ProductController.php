<?php
/**
 * \class	ProductController
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-18
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
namespace App\Http\Controllers\Product;

use Input;
use Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;


use App\Helpers\StoreHelper;
use App\Services\Products\ProductFactory;

use App\Jobs\BackInStock;
use App\Jobs\ResizeImages;
use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;

use App\Models\Image;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductType;
use App\Models\ProdImageMap;
use App\Models\Notification;
use App\Models\CategoryProduct;


use App\Traits\Logger;


/**
 * \brief MVC Controller to Handle the Product Administration functions.
 *
 * {INFO_2018-03-01} Removed all code except copy and product add 
 * {INFO_2018-03-04} Coded up Parent product view call
 */
class ProductController extends Controller
{
use Logger;


	/**
	 * Open log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-admin");
		$this->setClassName("ProductController");
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
	 * New Save method, create product controller from factory.
	 *
	 *
	 *
	 * @return	void
	 */
	public function Save(ProductRequest $request)
	{
		$this->LogFunction("Save()");

		$store = app('store');
		$store_id = $request->query('s',$store->id);
		$category_id = $request->query('c',0);
/*		$query = $request->input();
		foreach($query as $n=>$v)
		{
			if(is_string($n)== true)
			{
				if(is_string($v)== true)
				{
					$this->LogMsg("Checking query N= $n while V= $v");
					if($n=="s") $store_id = $v;
					if($n=="c") $category_id = $v;
				}
			}
		}
		$this->LogMsg("Required store ID [".$store_id."]");
		$this->LogMsg("Required Category ID [".$category_id."]");
*/
		$form = Input::all();
		$type = $form['prod_type'];
		$this->LogMsg("Build Product Factory");
		$controller = ProductFactory::build($type);
		$controller->Save($request);
		return Redirect::to("/admin/products");
	}



	/**
	 * New Update method, create prouct controller from factory.
	 *
	 *
	 * @param	App\Http\Requests\ProductRequest	$request
	 * @param	integer	$id
	 * @return	void
	 */
	public function Update(ProductRequest $request, $id)
	{
		$this->LogFunction("Update()");

		$store = app('store');
		$store_id = $request->query('s',$store->id);
		$category_id = $request->query('c',0);
/*
		$store_id = $store->id;
		$category_id = 0;
		$query = $request->input();
		foreach($query as $n=>$v)
		{
			if(is_string($n)== true)
			{
				if(is_string($v)== true)
				{
					$this->LogMsg("Checking query N= $n while V= $v");
					if($n=="s") $store_id = $v;
					if($n=="c") $category_id = $v;
				}
			}
		}
		$this->LogMsg("Required store ID [".$store_id."]");
		$this->LogMsg("Required Category ID [".$category_id."]");
*/
		$form = Input::all();
		$type = $form['prod_type'];
		$this->LogMsg("Build Product Factory for Type [".$type."]");
		$controller = ProductFactory::build($type);
		$controller->Update($request, $id);
		return Redirect::to("/admin/products");
	}




	/**
	 * New Delete method, delete product using factory.
	 *
	 * @param	App\Http\Requests\ProductRequest	$request
	 * @param	integer	$id
	 * @return	void
	 */
	public function Delete(ProductRequest $request, $id)
	{
		$this->LogFunction("Delete()");

		$store = app('store');
		$store_id = $request->query('s',$store->id);
		$category_id = $request->query('c',0);
/*
		$store_id = $store->id;
		$category_id = 0;
		$query = $request->input();
		foreach($query as $n=>$v)
		{
			if(is_string($n)== true)
			{
				if(is_string($v)== true)
				{
					$this->LogMsg("Checking query N= $n while V= $v");
					if($n=="s") $store_id = $v;
					if($n=="c") $category_id = $v;
				}
			}
		}
		$this->LogMsg("Required store ID [".$store_id."]");
		$this->LogMsg("Required Category ID [".$category_id."]");
*/
		$form = Input::all();
		$type = $form['prod_type'];
		$this->LogMsg("Build Product Factory");
		$controller = ProductFactory::build($type);
		$controller->Delete($request, $id);
		return Redirect::to("/admin/products");
	}

}
