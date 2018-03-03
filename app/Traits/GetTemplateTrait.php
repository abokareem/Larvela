<?php
/**
 * Larvela
 * Copyright (C) 2017
 * by Present & Future Holdings Pty Ltd Trading as Off Grid Engineering
 * https://off-grid-engineering.com
 *
 * \date	2017-10-10
 * \author	Sid Young <sid@off-grid-engineering.com>
 */
namespace App\Traits;


trait Template
{

	/**
	 * Given a template name for this Job
	 * get its ID and then find it in the mappings
	 *
	 * Usage:
	 *  $this->template_file_name = $this->getTemplate("7_DAY_ALERT_EMAIL");<br>
	 *  if(strlen($this->template_file_name)==0)<br>
	 *  {<br>
	 *      $this->template_file_name = "template_1_seven-day-alert.email";<br>
	 *  }<br>
	 *
	 * @param $name Name of action we are looking for
	 * @return string - template file name to load or empty string if nothing specified
	 */
	 public function getTemplate($name)
	 {
	 	$template_file_name = "";
		$TemplateActions = new \App\TemplateActions();
		$rows = $TemplateActions->getAll();
		$aid = 0;
		foreach($rows as $r)
		{
			if($r->action_name == $name)
			{
				$aid = $r->id;
				break;
			}
		}
		$TemplateMappings = new \App\TemplateMappings();
		$sid = 1;
		$rows = $TemplateMappings->getByStoreID($sid);
		foreach($rows as $r)
		{
			if($r->template_action_id == $aid)
			{
				$template_file_name = "template_".$sid."_".$r->template_name;
				break;
			}
		}
		return $template_file_name;
	}
}
