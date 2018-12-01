<?php
/**
 * \class	AttributesController
 * \date	2018-11-08
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version	1.0.2
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
 *
 *
 */
namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Session;
use Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\AttributeRequest;


use App\User;
use App\Models\Store;
use App\Models\Attribute;
use App\Models\Customer;



use App\Traits\Logger;

/**
 * \brief Administration dashboard controller.
 */
class AttributesController extends Controller
{
use Logger;

	/**
	 * Construct object and set logging up
	 * 
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("larvela-admin");
		$this->setClassName("AttributesController");
		$this->LogStart();
	}


	/**
	 * Close of log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}




	/**
	 * Display the product attributes for this store.
	 *
	 * @return	mixed
	 */
	public function ShowAttributesPage()
	{
		$this->LogFunction("ShowAttributesPage()");
		$store = app('store');
		$stores = Store::get();
		$attributes = Attribute::get();

		return view('Admin.Attributes.showattributes',[
			'store'=>$store,
			'stores'=>$stores,
			'attributes'=>$attributes
			]);
	}


	public function AddNew()
	{
		$this->LogFunction("addNew()");
		$store = app('store');
		$stores = Store::get();
		$attributes = Attribute::get();

		return view('Admin.Attributes.add_attribute',[
			'store'=>$store,
			'stores'=>$stores
			]);
	}


	/**
	 * GET ROUTE: /admin/attribute/edit/{id}
	 * 
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Edit($id)
	{
		$this->LogFunction("Edit()");
		$store = app('store');
		$stores = Store::get();
		$attribute = Attribute::find($id);
		return view('Admin.Attributes.editattribute',[
			'store'=>$store,
			'stores'=>$stores,
			'attribute'=>$attribute
			]);
	}



	/**
	 *
	 * POST ROUTE:	/admin/attribute/Save
	 *
	 * @param	App\Https\Requests\AttributeRequest	$request
	 * @return	mixed
	 */
	public function Save(AttributeRequest $request)
	{
		$this->LogFunction("Save()");
		$attribute = new Attribute;
		$attribute->attribute_name = $request['attribute_name'];
		$attribute->attribute_token = $request['attribute_token'];
		$attribute->store_id = $request['store_id'];
		$attribute->save();
		return redirect('/admin/attributes');
	}



	/**
	 *
	 * POST ROUTE:	/admin/attribute/udpate/{id}
	 *
	 * @param	App\Https\Requests\AttributeRequest	$request
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Update(AttributeRequest $request, $id)
	{
		$this->LogFunction("Update()");
		$attribute = Attribute::find($id);
		$attribute->attribute_name = $request['attribute_name'];
		$attribute->attribute_token = $request['attribute_token'];
		$attribute->store_id = $request['store_id'];
		$attribute->save();
		return redirect('/admin/attributes');
	}




	public function Delete(AttributeRequest $request, $id)
	{
		dd($this);
	}
}



