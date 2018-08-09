<?php
/**
 * \class TemplateService
 * @date 2016-12-12
 * @author Sid Young <sid@off-grid-engineering.com>
 */
namespace App\Services;


use App\Http\Requests\TemplateNewRequest;
use App\Http\Requests\TemplateUpdateRequest;

use App\Models\Template;


/**
 * \brief Service layer for the template model
 */
class TemplateService
{
	public static function insert(TemplateRequest $request)
	{
		$o = new Template;
		$rv = $o->save();
		if($rv > 0)
		{
			\Session::flash('flash_message','Template Saved!');
			return $o->id;
		}
		else
		{
			\Session::flash('flash_errore','Template Save FAILED!');
		}
		return $rv;
	}



	public static function update(TemplateRequest $request)
	{
		$Templates = new Templates;
		$rv = $Templates->UpdateTemplate($request);
		if($rv > 0)
		{
			\Session::flash('flash_message','Template updated successfully!');
		}
		else
		{
			\Session::flash('flash_error','Template update failed!');
		}
		return $rv;
	}
}
