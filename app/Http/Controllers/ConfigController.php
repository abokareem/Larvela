<?php
/**
 * \class ConfigController
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2016-12-15
 */
namespace App\Http\Controllers;

use Input;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Requests\StoreSettingsSaveRequest;


use App\Models\Store;
use App\Models\StoreSetting;

use App\Helpers\StoreHelper;

use App\Traits\Logger;



/**
 * \brief MVC Controller for handling basic configuration settings and functions.
 */
class ConfigController extends Controller
{
use Logger;



	/**
	 * Open log file
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->setFileName("store-admin");
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
	 * Get all confgiuration and return a view listing them.
	 * On the form we can change the store so capture the query parameter and
	 * reload for the requested store.
	 *
	 * GET ROUTE: /admin/settings
	 *
	 * @param	App/Http/Request	$request
	 * @return	mixed
	 */
	public function Show(Request $request)
	{
		$this->LogFunction("Show");

		$store = app('store');
		$stores = Store::all();
		$store_id = $store->id;
		$this->LogMsg("Default store ID [".$store->id."]");

		#
		# get global (store id = 0) and the current store settings.
		#
		$global_settings = StoreSetting::where('setting_store_id',0)->get();

		$query = $request->input();
		foreach($query as $n=>$v)
		{
			if(is_string($n)== true)
			{
				if(is_string($v)== true)
				{
					$this->LogMsg("Checking query N= $n while V= $v");
					if($n=="s") $store_id = $v;
				}
			}
		}
		$this->LogMsg("Required store ID [".$store_id."]");
		$settings = StoreSetting::where('setting_store_id',$store_id)->get();

		return view('Admin.Settings.showsettings',[
			'store'=>$store,
			'stores'=>$stores,
			'store_id'=>$store_id,
			'settings'=>$settings,
			'global_settings'=>$global_settings
			]);
	}




    /**
	 * Edit a particular setting given the row ID
	 * GET ROUTE: /admin/setting/edit/{id}
	 *
	 * @param	App/Http/Request	$request
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Edit(Request $request, $id)
	{
		$this->LogFunction("Edit()");

		$store = app('store');
		$stores = Store::all();
		$setting = StoreSetting::find($id);
		return view('Admin.Settings.editsetting',[
			'store'=>$store,
			'stores'=>$stores,
			'setting'=>$setting,
			'store_id'=>$setting->setting_store_id
			]);
	}




	/**
	 *
	 * POST ROUTE: /admin/setting/edit/{id}'
	 *
	 * @param	App/Http/Request	$request
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Update(Request $request, $id)
	{
		$this->LogFunction("Update()");
		$o = StoreSetting::find($id);
		$form = Input::all();
		$o->setting_name = $form['setting_name'];
		$o->setting_value= $form['setting_value'];
		$o->setting_store_id= $form['store_id'];
		if(($rv=$o->save())>0)
		{
			\Session::flash('flash_message','Setting updated!');
		}
		else
		{
			\Session::flash('flash_error','Setting not updated!');
		}
		return $this->Show($request);
	}



	/**
	 * Display the settings page
	 *
	 * GET ROUTE: /admin/setting/addnew
	 *
	 * @return	mixed
	 */
	public function Add()
	{
		$this->LogFunction("Add()");
		$store = app('store');
		$store_id = $store->id;
		$stores = Store::all();
		return view('Admin.Settings.addsettings',[
			'store'=>$store,
			'stores'=>$stores,
			'store_id'=>$store_id
			]);
	}




	/**
	 * Given the settings data, insert it into the store_settings table
	 *
	 * POST ROUTE: /admin/setting/addnew
	 *
	 * @param	App/Httpd/Requests/StoreSettingsSaveRequest	$request
	 * @return	mixed
	 */
	public function Save(StoreSettingsSaveRequest $request)
	{
		$this->LogFunction("Save()");
		$o = new StoreSetting;
		$form = Input::all();
		$o->setting_name = $form['setting_name'];
		$o->setting_value= $form['setting_value'];
		$o->setting_store_id= $form['store_id'];
		if(($rv=$o->save())>0)
		{
			\Session::flash('flash_message','Setting Saved!');
		}
		else
		{
			\Session::flash('flash_error','Failed to save Setting!');
		}
		return $this->Show($request);
	}


	/**
	 * Given the row ID, delete the settings data
	 *
	 * POST ROUTE: /admin/setting/delete/{id}
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Delete(Request $request, $id)
	{
		$this->LogFunction("Delete(".$id.")");
		StoreSetting::find($id)->delete();
		return $this->Show($request);
	}
}
