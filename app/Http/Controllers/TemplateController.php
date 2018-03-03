<?php
/**
 * \class TemplateController
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2016-09-15
 */
namespace App\Http\Controllers;


use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\TemplateRequest;


use App\Models\Store;
use App\Models\Template;
use App\Models\TemplateAction;
use App\Models\TemplateMapping;



/**
 * \brief Controller for all template handling operations.
 *
 * Templates are used in all email communications via the Jobs mechanism in Laravel.
 * The mapping table maps a template file to an action.
 */
class TemplateController extends Controller
{


	/**
	 * Show all assigned template mappings.
	 *
	 * GET ROUTE: /templates
	 *
	 * @return	mixed
	 */
	public function Show(Request $request)
	{
		$store_id = 0;
		$query = $request->input();

		foreach($query as $n=>$v)
		{
			if($n=="s") $store_id = $v;
		}
		$actions = TemplateAction::all();
		$mappings = TemplateMapping::all();

		$templates = array();

		$template_dir = base_path()."/"."templates/";
		if(file_exists($template_dir))
		{
			$files = scandir($template_dir);
			foreach($files as $f)
			{
				if($f == ".") continue;
				if($f == "..") continue;

				$temp_path = $template_dir.$f;

				$template = new \stdClass;
				$template->name = $f;
				$template->file_size = filesize($temp_path);
				$template->date_modified = date("Y-m-d", filemtime($temp_path));
				$template->time_modified = date("H:i:s", filemtime($temp_path));
				$template->date_created  = date("Y-m-d", filectime($temp_path));
				$template->time_created  = date("H:i:s", filectime($temp_path));
				$template->mapping_id= 0;
				$template->action_id = 0;
				$template->store_id  = 0;
				foreach($mappings as $m)
				{
					$length = strlen($f);
					$file_name = $f;
					$parts = explode("_", $f);
					if(sizeof($parts)>2)
					{
						$pre_length = strlen($parts[0])+strlen($parts[1])+2;
						$file_name = substr($f, $pre_length );
					}

					if($m->template_name == $file_name)
					{
						$template->mapping_id = $m->id;
						$template->action_id  = $m->template_action_id;
						$template->store_id   = $m->template_store_id;
					}
				}
				array_push($templates, $template);
			}
		}
		$stores = Store::all();

		return view('Admin.Templates.listtemplates',[
			'templates'=>$templates,
			'actions'=>$actions,
			'mappings'=>$mappings,
			'stores'=>$stores,
			'selected'=>$store_id
			]);
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
	public function Save(TemplateRequest $request)
	{
		$TemplateMappings = new TemplateMappings;

		$action = $request['actions'];
		$content = $request['content'];
		$filename = $request['filename'];
		$store_id = $request['store_id'];

		$tfn = public_path()."/"."template_".$store_id."_".$filename;
		if(file_exists($tfn))
		{
			$today = date("YmdHis");
			$copy = public_path()."/backuptemplate_".$tfn."-".$today;
			if(copy($tfn,$copy))
			{
				file_put_contents($tfn,$content);
				\Session::flash('flash_message','Template saved!');
			}
			else
				\Session::flash('flash_error','Cannot make backup copy of template - data not saved.!');
		}
		else
		{
			file_put_contents($tfn,$content);
			\Session::flash('flash_message','Template saved!');
		}
		$o = new TemplateMapping;
		$o->template_name = $filename;
		$o->template_action_id = $action;
		$o->template_store_id = $store_id;
		$o->save();
		return $this->Show();
	}




	/**
	 * Return a view showing the "Add New Template" form. Pass a list of Actions that the template can be matched to.
	 *
	 * GET ROUTE: /admin/template/new
	 *
	 * @return	mixed
	 */
	public function Add()
	{
		$actions = TemplateAction::all();
		return view('Admin.Templates.addtemplate',['actions'=>$actions]);
	}




	/**
	 * make a backup of the file and then check if it is being
	 * assigned to a DB mapping or not.
	 *
	 * POST ROUTE: /admin/template/save
	 *
	 * @param	TemplateRequest	$request
	 * @return	mixed
	 */
	public function Update(TemplateRequest $request)
	{
		$TemplateMappings = new TemplateMappings;

		$filename = $request['filename'];
		$mid = $request['mid'];
		$content = $request['content'];
		$store_id = $request['store_id'];
		$action = $request['actions'];

		$tfn = public_path()."/"."template_".$store_id."_".$filename;
		$today = date("YmdHis");
		$copy = public_path()."/backuptemplate_".$store_id."_".$filename."-".$today;

		if(file_exists($tfn))
		{
			copy($tfn,$copy);
		}

		/*
		 * if action == 1 then entry is set to removed (not Assigned)
		 * if action >1 then entry is assigned to an action (may already be assigned to an action!)
		 */
		if($action==1)
		{
			if($mid!=0)
			{
				#$row = $TemplateMappings->get By ID($mid); # {FIX_2017-10-25} Converted calls to Eloquent where clauses in UpdateTemplate()
				$row = TemplateMappings::where('id', $mid)->first();
				$TemplateMappings->DeleteByAIDSID($row->template_action_id, $row->template_store_id);
				$text = "Removing mapping for ".$filename;
				\Session::flash('flash_message', $text );
			}
			else
			{
				$text = "ERROR - Invalid selection";
				\Session::flash('flash_error', $text );
			}
		}
		else /* action != 1 */
		{
			if($mid==0)
			{
				#
				# remove current mapping and set this as the action.
				#
				$TemplateMappings->DeleteByAIDRID($action, $user->reseller_id);
				$d = array('template_name'=>$filename,'template_action_id'=>$action,'template_store_id'=>$store_id);
				$TemplateMappings->InsertMapping($d);
				$text = "Inserting new mapping for ".$filename;
				\Session::flash('flash_message', $text );
			}
			else /* mid != 0 = entry in DB */
			{
				$row = TemplateMappings::where('id', $mid)->first();
				if($row->template_action_id != $action)
				{
					$TemplateMappings->DeleteByAIDRID($row->template_action_id, $user->reseller_id);
					$d = array('template_name'=>$filename,'template_action_id'=>$action,'template_reseller_id'=>$user->reseller_id);
					$TemplateMappings->InsertMapping($d);
					$text = "Replacing mapping with ".$filename;
					\Session::flash('flash_message', $text );
				}
			}
		}
		return $this->ShowTemplates();
	}



	/**
	 * Need file from disk + any DB entries.
	 *
	 * NOTE: An un-implemented template may not yet have a mapping to an action.
	 *
	 * GET ROUTE: /admin/template/edit/<name>
	 *
	 * @return	mixed
	 */
	public function Edit($id)
	{
		$actions = TemplateAction::all();

		$template_file_name = "email-".$id.".template";
		$tfn = public_path()."/".$template_file_name;
		$content="";
		try
		{
			if(file_exists($tfn))
			{
				$content = file_get_contents( $tfn );
			}
			else
			{
				return $this->Show();
			}
		}
		catch (Exception $e)
		{
			return $this->Show();
		}
		$mid = 0;
		$aid = 0;
		foreach($mappings as $m)
		{
			if($m->template_name == $id)
			{
				$aid = $m->template_action_id;
				$mid = $m->id;
			}
		}
		return view('Admin.Templates.edittemplate',[
			'content'=>$content,
			'actions'=>$actions,
			'mid'=>$mid,
			'filename'=>$id]);
	}


}
