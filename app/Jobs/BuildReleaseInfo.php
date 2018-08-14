<?php
/**
 * \class	BuildReleaseInfo
 * \date	2017-10-24
 * \version	1.0.0
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
 * This job builds nightly release json data for code info and release notes.
 * Every file is checked for FIX and INFO tags.
 *
 * {FIX_<YYYY-MM-DD>_<TICKET-ID>} <Message text>
 * {INFO_<YYYY-MM-DD>} <Message text>
 *
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Traits\Logger;

/**
 * \brief builds nightly release json data for code info and release notes.
 */
class BuildReleaseInfo implements ShouldQueue
{
use Logger;
use InteractsWithQueue, Queueable, SerializesModels;

/**
 * Release info tags [ file, date, info ]
 * @var array $release_info
 */
protected $release_info;


/**
 * Code Fixes data [file,date,info]
 * @var array $fixes
 */
protected $fixes;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->setFileName("release-info");
		$this->LogStart();

		$this->release_info = array();
		$this->fixes = array();
    }


    /**
     * Close log file off
     *
     * @return void
     */
    public function __destruct()
    {
		$this->LogEnd(); 
    }



    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$this->LogMsg("Start scaning directories");
		$this->LogMsg("App path: [".app_path()."]");

		$directories = array("Traits","Mail","Services","Models","Jobs","Providers","Helpers","Payments","Listeners","Console","Events","Http/Controllers");
		foreach($directories as $dir)
		{
			$path = app_path()."/".$dir;
			$this->LogMsg("Checking [".$path."]");

			$glob = $path."/*.php";
			foreach( glob( $glob ) as $file)
			{
				$this->LogMsg("Checking [".$file."]");
				if(is_file($file))
				{
					$path_parts = pathinfo($file);
					if(($path_parts['filename'] == "BuildReleaseInfo") &&
					  ($path_parts['extension'] == "php")) continue;
					$this->CheckFile($file);
				}
			}
		}
		$this->LogMsg("Done!");

		#
		# temporary dump
		# @todo Generate a JSON file and dump in PUBLIC Directory.
		$this->LogMsg("Report Dump:");
		$this->LogMsg( print_r($this->fixes, true) );
		$this->LogMsg( print_r($this->release_info, true) );
    }


	/**
	 * given a file, check if it has any release notes or fixes to document
	 *
	 * @param	string	$file
	 * @return	void
	 */
	protected function CheckFile($file)
	{
		$this->LogMsg("Checking file [".$file."] for FIX and INFO tags.");

		$pattern ='/FIX_/';
		$fh = fopen($file,'r');
		while (!feof($fh))
		{
			$line = fgets($fh, 4096);
			if(preg_match($pattern, $line))
			{
				$pos = strpos($line, "{FIX_");
				$date = substr($line,5+$pos,10);
				$this->LogMsg("Date [".$date."]");
				$this->fixes[] = array($file,$date,substr($line,$pos));
			}
		}
		fclose($fh);
		$pattern ='/INFO_/';
		$fh = fopen($file,'r');
		while (!feof($fh))
		{
			$line = fgets($fh, 4096);
			if(preg_match($pattern, $line))
			{
				$pos = strpos($line, "{INFO_");
				$date = substr($line,6+$pos,10);
				$this->LogMsg("Date [".$date."]");
				$this->release_info[] = array($file,$date,substr($line,$pos));
			}
		}
		fclose($fh);
	}
}
