<?php
/**
 * \class	SEOController
 * @author	Sid Young <sid@off-grif-engineering.com>
 * @date	2016-08-23
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use App\Http\Requests;
use App\Http\Requests\SeoRequest;


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
		$Seo = new Seo;

		$data = array( 'id'=>$id,
			'seo_token'=>$request['seo_token'],
			'seo_html_data'=>$request['seo_html_data'],
			'seo_status'=>$request['seo_status'],
			'seo_edit'=>$request['seo_edit'],
			'seo_store_id'=>$request['seo_store_id']
			);
		$rv = $Seo->UpdateSeo($data);
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
	 * @param mixed Request Object after validation has occured
	 * @return mixed view object
	 */
	public function SaveNewSEO(SeoRequest $request)
	{
		$Seo = new Seo;

		$rows = $Seo->getByStoreToken($request['seo_store_id'],$request['seo_token']);
		if(sizeof($rows)>0)
		{
			\Session::flash('flash_error',"ERROR - Token already defined for that store");
			return $this->ShowSEOList();
		}
		else
		{
			$data = array(
				'seo_token'=>$request['seo_token'],
				'seo_html_data'=>$request['seo_html_data'],
				'seo_status'=>$request['seo_status'],
				'seo_edit'=>$request['seo_edit'],
				'seo_store_id'=>$request['seo_store_id']
				);
			$rv = $Seo->InsertSeo($data);
			switch($rv)
			{
				case ($rv>0):
					\Session::flash('flash_message',"Seo Data Save.");
					break;
				default:
					\Session::flash('flash_error',"ERROR - Unable to save?");
					break;
			}
		}
		return $this->ShowSEOList();
	}



	/**
	 * GET ROUTE: /admin/seo
	 */
	public function ShowSEOList()
	{
		$blocks = Seo::all();
		return view('Admin.SEO.listseo',['blocks'=>$blocks]);
	}


	/**
	 * GET ROUTE: /admin/seo/edit/{id}
	 */
	public function ShowEditPage($id)
	{
		$Seo = new Seo;
		$Store = new Store;

		# {FIX_2017-10-25} ShowEditPage() - Refactored to use Eloquent calls
		$block = Seo::find($id); 
		#
		$stores = $Store->getSelectList("seo_store_id", $block->seo_store_id, true);

		return view('Admin.SEO.editseo',['seoblock'=>$block,'store_select_list'=>$stores]);
	}


	/**
	 * GET ROUTE: /admin/seo/addnew
	 */
	public function ShowAddSEO()
	{
		$Store = new Store;

		$list = $Store->getSelectList("seo_store_id");
		return view('Admin.SEO.addseo',['stores_list'=>$list]);
	}
}
