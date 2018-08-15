<?php
/**
 *
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-01-01
 * \version 1.0.0
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * \addtogroup  Traits
 * Logger - Generic Logger class, not PSR compliant but does the job nicely.
 */
namespace App\Traits;


/**
 * \brief Generic Logger class, not PSR compliant but does the job nicely.
 */
trait Logger
{
protected $LOGDIR= '/logs';
protected $FILENAME = "default";
protected $OWNER = "apache";
protected $GROUP = "apache";
protected $CLASSNAME = "";


	/**
	 *
	 *
	 * @return	void
	 */
	public function setFileName($name)
	{
		if(strlen($name) > 2)
		{
			$this->FILENAME = $name;
		}
	}


	/**
	 *
	 *
	 * @return  void
	 */
	public function setClassName($name)
	{
		$this->CLASSNAME = $name;
	}



	/**
	 *
	 *
	 * @return	void
	 */
	public function getFileName()
	{
		return $this->FILENAME;
	}




	/**
	 *
	 *
	 * @return	void
	 */
	public function getLogDir()
	{
		return $this->LOGDIR;
	}




	/**
	 *
	 *
	 * @return	void
	 */
	public function Log($message,$status)
	{
		if(is_array($message)==true)
		{
			$message = print_r($message,true);
		}
		if(!file_exists($this->LOGDIR))
		{
			mkdir($this->LOGDIR, 0777, true);
		}
		$today = date('Y-m-d');
		$now = date('H:i:s');
		#
		# WAS $str = $today.'|'.$now.'|'.$status.'|'.$message."\r\n";
		#
		$str = $today."|".$now."|".$status;
		if(strlen($this->CLASSNAME)>2)
		{
			$str .= "|".$this->CLASSNAME."|".$message."\r\n";
		}
		else
		{
			$str .= "|".$message."\r\n";
		}
		$LOGFILE = $this->LOGDIR.'/'.$this->FILENAME."-".$today.".log";
		file_put_contents($LOGFILE, $str, FILE_APPEND);
		chown($LOGFILE,"apache");
		chgrp($LOGFILE,"apache");
	}




	/**
	 *
	 *
	 * @return	void
	 */
	public function LogFunction($message)
	{
		$this->Log($message, "FUNC");
	}




	/**
	 *
	 *
	 * @return	void
	 */
	public function LogMsg($message)
	{
		$this->Log($message, "OK");
	}




	/**
	 *
	 *
	 * @return	void
	 */
	public function LogError($message)
	{
		$this->Log($message, "ERR");
	}




	/**
	 *
	 *
	 * @return	void
	 */
	public function LogWarn($message)
	{
		$this->Log($message, "WARN");
	}




	/**
	 *
	 *
	 * @return	void
	 */
	public function LogStart()
	{
		$this->Log("================ START =================", "START");
	}




	/**
	 *
	 *
	 * @return	void
	 */
	public function LogEnd()
	{
		$this->Log("====================================", "END");
	}

}
