<?php
/**
 * \class	AdvertController
 * @date	2016-12-08
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Input;

use App\Http\Requests\AdvertRequest;



use App\Services\AdvertService;


use App\Models\Advert;
use App\Models\Store;


/**
 * \brief MVC Controller for the Advertisement fucntions.
 */
class AdvertController extends Controller
{


	/**
	 * GET ROUTE: /admin/adverts
	 *
	 * @return	mixed
	 */
	public function ShowAdvertsPage()
	{
		$adverts = Advert::all();
		return view('Admin.Adverts.showadverts',['adverts'=>$adverts]);
	}




	/**
	 * GET ROUTE: /admin/advert/add
	 *
	 * @return	mixed
	 */
	public function ShowAddAdvertPage()
	{
		$store = app('store');
		$stores = Store::all();
		return view('Admin.Adverts.addadvert',[ 'store'=>$store, 'stores'=>$stores ]);
	}




	/**
	 * GET ROUTE: /admin/advert/edit/{id}
	 *
	 * @return	mixed
	 */
	public function ShowEditAdvertPage($id)
	{
		$advert = Advert::find($id);
		$store = app('store');
		$stores = Store::all();
		return view('Admin.Adverts.editadvert',[
			'advert'=>$advert,'store'=>$store,'stores'=>$stores
			]);
	}




	/**
	 * POST ROUTE: /admin/advert/save
	 *
	 * @return	mixed
	 */
	public function SaveNewAdvert(AdvertRequest $request)
	{
		AdvertService::insert($request);
		return $this->ShowAdvertsPage();
	}




	/**
	 * POST ROUTE: /admin/advert/update/{id}
	 *
	 * @return	mixed
	 */
	public function UpdateAdvert(AdvertRequest $request, $id)
	{
		$request['id'] = $id;
		AdvertService::update($request);
		return $this->ShowAdvertsPage();
	}
}
