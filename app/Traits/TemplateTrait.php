<?php
/**
 * @date 2016-12-31
 * @author	Sid Young	<sid@off-grid-engineering.com>
 */
namespace App\Traits;

use App\Models\TemplateAction;
use App\Models\TemplateMapping;


/**
 * \brief Trait to handle template file building.
 *
 * template_mapping;
 +--------------------+------------------+------+-----+---------+----------------+
 | Field              | Type             | Null | Key | Default | Extra          |
 +--------------------+------------------+------+-----+---------+----------------+
 | id                 | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
 | template_name      | varchar(255)     | NO   |     | NULL    |                |
 | template_action_id | int(11)          | NO   | UNI | NULL    |                |
 +--------------------+------------------+------+-----+---------+----------------+
 */
trait TemplateTrait
{
	/**
	 * Given a template name find a mapping if available and return a new template file name to load
	 * If not found, return an empty string.
	 *
	 * Usage:
	 *  $this->template_file_name = $this->getTemplate("7_DAY_ALERT_EMAIL");<br>
	 *  if(strlen($this->template_file_name)==0)<br>
	 *  {<br>
	 *      $this->template_file_name = "template_1_seven-day-alert.email";<br>
	 *  }<br>
	 *
	 * @param	string	$action_name
	 * @return	string
	 */
	 public function getTemplate($action_name)
	 {
	 	$template_file_name = "";
		$actions = TemplateAction::all();
		foreach($actions as $action)
		{
			if($action->action_name == $action_name)
			{
				$mapping = TemplateMapping::where('template_action_id',$action->id)->first();
				if(sizeof($mapping) >0)
				{
					$template_file_name = $mapping->template_name;
				}
				break;
			}
		}
		return $template_file_name;
	}
}
