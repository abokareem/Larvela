<?php
/**
 * \class	SEOController
 * \author	Sid Young <sid@off-grif-engineering.com>
 * \date	2016-08-23
 * \version	1.0.0
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
 * \addtogroup SEO
 * SEOController - The SEO Controller is responsible for managing SEO content.
 * - Security has not yet been added to this controller.
 */
namespace App\Http\Controllers;

use Input;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\SeoRequest;
use App\Http\Controllers\Controller;


use App\Models\Seo;
use App\Models\Store;




/**
 * \brief SEO/CMS Block handling controller
 */
class SEOController extends Controller
{

	/**
	 * Update the SEO table for a certain row
	 * Validate ID and then update row.
	 *
	 * POST ROUTE: /admin/seo/update/{id}
	 *
	 * @param mixed Request Object after validation has occured
	 * @return mixed view object
	 */
	public function UpdateSeo(SeoRequest $request, $id)
	{
		$seo = Seo::find($id);
		$seo->seo_token = $request['seo_token'];
		$seo->seo_html_data = $request['seo_html_data'];
		$seo->seo_status = $request['seo_status'];
		$seo->seo_edit = $request['seo_edit'];
		$seo->seo_store_id = $request['seo_store_id'];
		$rv = $seo->save();
		switch($rv)
		{
			case ($rv>0):
				\Session::flash('flash_message',"Seo data updated.");
				break;
			case ($rv==0):
				\Session::flash('flash_message',"No Changes made!");
				break;
			default:
				\Session::flash('flash_error',"ERROR - Row update failed");
				break;
		}
		return $this->ShowSEOList();
	}



	/**
	 * Save the SEO block back to the Database
	 *
	 * @param	mixed	$request -  Request Object after validation has occured
	 * @return	mixed
	 */
	public function SaveNewSEO(SeoRequest $request)
	{
		$rows = Seo::where('seo_store_id',$request['seo_store_id'])
			->where('seo_token',$request['seo_token'])->get();
		if(sizeof($rows)>0)
		{
			\Session::flash('flash_error',"ERROR - Token already defined for that store");
			return $this->ShowSEOList();
		}
		else
		{
			$o = new Seo;
			$o->seo_token = $request['seo_token'];
			$o->seo_html_data = $request['seo_html_data'];
			$o->seo_status = $request['seo_status'];
			$o->seo_edit = "Y";
			$o->seo_store_id = $request['seo_store_id'];
			if($o->save()>0)
			{
				\Session::flash('flash_message',"Seo Data Save.");
			}
			else
			{
				\Session::flash('flash_error',"ERROR - Unable to save?");
			}
		}
		return $this->ShowSEOList();
	}



	/**
	 * GET ROUTE: /admin/seo
	 *
	 * @return	mixed
	 */
	public function ShowSEOList()
	{
		$blocks = Seo::all();
		$store = app('store');
		$stores = Store::all();
		return view('Admin.SEO.listseo',['blocks'=>$blocks]);
	}



	/**
	 * GET ROUTE: /admin/seo/edit/{id}
	 * {FIX_2017-10-25} ShowEditPage() - Refactored to use Eloquent calls
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function ShowEditPage($id)
	{
		$store = app('store');
		$stores = Store::all();
		$block = Seo::find($id); 

		return view('Admin.SEO.editseo',[
			'store'=>$store,
			'seoblock'=>$block,
			'stores'=>$stores
			]);
	}



	/**
	 * GET ROUTE: /admin/seo/addnew
	 */
	public function ShowAddSEO()
	{
		$store = app('store');
		$stores = Store::all();
		return view('Admin.SEO.addseo',['store'=>$store, 'stores'=>$stores]);
	}
}
