<?php
/**
 * \class	TemplateActionController
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-01-21
 */
namespace App\Http\Controllers;


use Input;
use Session;
use Illuminate\Http\Request;
use App\Http\Requests;


use App\Models\Store;
use App\Models\TemplateAction;



/**
 * \brief Controller for all the actions/business process operations.
 */
class TemplateActionController extends Controller
{


	/**
	 * Show all assigned template mappings.
	 *
	 * GET ROUTE: /admin/actions/show
	 *
	 * @return	mixed
	 */
	public function Show()
	{
		$stores = Store::all();
		$actions = TemplateAction::all();
		return view('Admin.Templates.showactions',['actions'=>$actions,'stores'=>$stores]);
	}




	/**
	 * User has added a template view a view,
	 * if the HTML content field of the form is >0 then save it to a file.
	 *
	 * Before saving it to a file, if a template already exists then make a backup copy of the template file.
	 *
	 * POST ROUTE: /admin/template/save
	 *
	 * @param	TemplateRequest	$request
	 * @return	mixed
	 */
	public function Save()
	{
		$form = \Input::all();
		$o = new TemplateAction;
		$n1 = strtoupper(trim($form['action_name']));
		$n2 = str_replace(' ', '_', $n1);
		$action_name = preg_replace('/[^A-Za-z0-9\-\_]/', '', $n2);
		if(strlen($action_name) > 3)
		{
			$o->action_name = $action_name;
			if(($rv=$o->save())>0)
			{
				\Session::flash('flash_message','System Process/Action saved!');
			}
			else
			{
				\Session::flash('flash_error','ERROR - save to database failed!');
			}
		}
		else
		{
			\Session::flash('flash_error','ERROR - Action name too short!');
		}
		return $this->Show();
	}



	/**
	 * Return a view showing the "Add New Template" form. Pass a list of Actions that the template can be matched to.
	 *
	 * GET ROUTE: /admin/action/add
	 *
	 * @return	mixed
	 */
	public function Add()
	{
		$actions = TemplateAction::all();
		return view('Admin.Templates.addaction',['actions'=>$actions]);
	}



	/**
	 *
	 * POST ROUTE: /admin/action/update/{id}
	 *
	 * @param	TemplateRequest	$request
	 * @return	mixed
	 */
	public function Update($id)
	{
		$o = TemplateAction::find($id);
		$form = Input::all();
		$o->action_name = trim(strtoupper($form['action_name']));
		if(($rv=$o->save())>0)
		{
			\Session::flash('flash_message','System Process/Action updated!');
		}
		else
		{
			\Session::flash('flash_error','ERROR - Update failed!');
		}
		return $this->Show();
	}



	/**
	 *
	 * GET ROUTE: /admin/action/edit/{id}
	 *
	 * @return	mixed
	 */
	public function Edit($id)
	{
		$action = TemplateAction::find($id);
		return view('Admin.Templates.editaction',['action'=>$action]);
	}
}
