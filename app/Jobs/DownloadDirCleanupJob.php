<?php
/**
 * \class	DownloadDirCleanupJob
 * \date	2018-09-26
 * \author	Sid Young <suid@off-grid-engineering.com>
 * \version	1.0.2
 *
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
 * \addtogroup CRON
 * Delete files in public/download/[0-F]/<GUID> that are more than "X" days old.
 *
 * Step 1:
 * - If the initial path does not exist, create it (for first time running).
 * - If the initial download/[0-F] path does not exist, create it also.
 * - Log ALL activity
 *
 * Step 2:
 * - Iterate through the 0-F directory structure and look for files, check creation time and remove them is old.
 * - If there are no files in the directory, and the directory is OLD then remove it.
 * - Log ALL activity
 */
namespace App\Jobs;


use App\Models\StoreSetting;
use App\Traits\Logger;


/**
 * \brief CRON task to cleanup old files in the public/download/[0-F]/<GUID> directory structure
 * {INFO_2018-09-30} Add code to support store setting DOWNLOAD_LIFESPAN
 */
class DownloadDirCleanupJob extends Job
{
use Logger;


/**
 * Keep downloaded virtual products in public for this number of days.
 * @var integer $remove_after_days
 */
protected $remove_after_days=7;


    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct()
    {
		$this->setFileName("larvela-cron");
		$this->setClassName("DownloadDirCleanupJob");
		$this->LogStart();
		$store = app('store');
		$settings = StoreSetting::where('setting_store_id',$store->id)->get()->toArray();
		$this->LogMsg("There are [".sizeof($settings)."] store settings to check.");
		if(!is_null($settings))
		{
		$this->remove_after_days = array_reduce( $settings,
			function($default,$s)
			{
				$this->LogMsg("Found [".$s['setting_name']."]");
				return ($s['setting_name']=="DOWNLOAD_LIFESPAN") ? $s['setting_value'] : $default;
			},$this->remove_after_days);
		}
		$this->LogMsg("Remove files after [".$this->remove_after_days."] days.");
    }

    /**
     * Execute the job by calling the Run() method.
     *
     * @return void
     */
    public function handle()
    {
		$this->Run();
    }



    /**
	 * Scan the public/download folder for sub dirs that have old files and remove the old files.
	 * - Create the sub folders as needed.
	 *
	 * @pre The "public" directory must exist
     * @return void
     */
	public function Run()
	{
		$purge_time = strtotime("-".$this->remove_after_days." days");
		$this->LogMsg("Purge time [".$purge_time."]");
		$this->LogMsg("Fetch base dir and check for required sub dirs.");
		$base = base_path()."/public/download";
		$this->CheckCreateDir($base);

		$this->LogMsg("Check/Create GUID directories");
		for($i=0;$i<17;$i++)
		{
			$guid_path = base_path()."/public/download/".dechex($i);
			$this->LogMsg("Checking for [".$guid_path."]");
			if(!is_dir($guid_path))
			{
				$this->LogMsg("Create path for [".$guid_path."]");
				$this->CheckCreateDir($guid_path);
			}
		}

		$this->LogMsg("Checking each download directory:");
		for($i=0;$i<17;$i++)
		{
			$full_guid_path = base_path()."/public/download/".dechex($i);
			$guid_path = "/public/download/".dechex($i);
			$this->LogMsg("Checking [".$guid_path."]");
			if(is_dir($full_guid_path))
			{
				$guid_dirs = $this->FindDirs($full_guid_path);
				$count = sizeof($guid_dirs);
				if(($count > 0) && (is_array($guid_dirs)))
				{
					$this->LogMsg(" - Found [".$count."] directories to check.");
					foreach($guid_dirs as $directory)
					{
						$temp_path = $full_guid_path."/".$directory;
						if(is_dir($temp_path))
						{
							$this->LogMsg(" - Found [".$directory."]");
							$files = $this->FindDirs($temp_path);
							$fcount = sizeof($files);
							$this->LogMsg(" - file and dir count [".$fcount."]");
							if($fcount==0) continue;
							foreach($files as $f)
							{
								$file_path = $temp_path."/".$f;
								$this->LogMsg(" - file [".$f."] mtime [".filemtime($file_path)."]");
								if(filemtime($file_path) < $purge_time)
								{
									$this->LogMsg(" - Remove file [".$f."]");
									unlink($file_path);
								}
							}
						}

					}
				}
			}
		}
		$this->LogEnd();
		return 0;
	}




	/**
	 * Give a path, return files and dirtectories as an array
	 * but not the current directory or previous directory.
	 *
	 * @return	array
	 */
	protected function FindDirs($path)
	{
		$found = array();
		if(is_dir($path))
		{
			$dirs_found = scandir($path);
			foreach($dirs_found as $d)
			{
				if(($d == ".")||($d == "..")) continue;
				array_push($found, $d);
			}
		}
		return $found;
	}




	/**
	 *
	 *
	 * @return	void
	 */
	protected function CheckCreateDir($directory)
	{
		try
		{
			if(!is_dir($directory))
			{
				$this->LogMsg("Creating [".$directory."]");
				mkdir($directory, 0750, true);
			}
		}
		catch (\Exception $e)
		{
				$this->LogMsg("ERROR - Exception thrown trying to create dir - Check filesystem!");
		}
	}
}
