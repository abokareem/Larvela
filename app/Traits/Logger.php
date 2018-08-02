<?php
/**
 * Larvela eCommerce Engine
 * Copyright (C) 2017-2018
 * by Present & Future Holdings Pty Ltd Trading as Off Grid Engineering
 * https://off-grid-engineering.com
 *
 *
 * \addtogroup  Trait
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
		$str = $today.'|'.$now.'|'.$status.'|'.$message."\r\n";
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
