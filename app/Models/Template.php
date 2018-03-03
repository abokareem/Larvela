<?php
/**
 * \class	Template
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-09-15
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Template file fetching methods - Not yet fully implemented.
 */
class Template
{
	/**
	 * Look in the templates directory and fetch all template file names.
	 *
	 * @return $array of file names available
	 */
	public function getAllTemplates()
	{
		$templates = array();
		$ppath = public_path();
		$path = $ppath."/templates";
		if(is_dir($path))
		{
			if($dir_res = opendir($path))
			{
				while(($file = readdir($dir_res)) !== false)
				{
					#template_<ID>_
					#012345678901234567890
					if(strlen($file)>8)
					{
						$parts = explode("_",$file);
						if(sizeof($parts)>0)
						{
							if($parts[0]=="template")
							{
								array_push($templates, $file);
							}
						}
					}
				}
			}
		}
		return $templates;
	}





}
