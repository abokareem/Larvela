<?php
/**
 * \class	AjaxController
 * \date	2017-09-12
 * \author	Sid Young
 *
 *
 *
 */
namespace App\Http\Controllers;

use Request;
use App\Http\Requests;
use Input;

use App\Traits\Logger;


/**
 * \brief	Provides AJAX handling for various store/view functionality
 */
class AjaxController extends Controller
{
use Logger;



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setFileName('larvela-ajax');
		$this->setClassName("AjaxController");
		$this->LogStart();
    }




	/**
     * Close of log file
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->LogEnd();
	}




	/**
	 * Called when a successful paypal transaction has occured and the Paypal API returns
	 * a stack of data. Decode it here and put it into an associative array for manipulation later.
	 *
	 *
	 * @return	string
	 */
	public function RecordPaypalData()
	{
		$this->LogFunction("RecordPaypalData()");

		$paypal = array();
		$part1 = "";
		$part2 = "";
		if(Request::ajax())
		{
			$paypal = Input::all();
			$this->LogMsg("PRINTR [".print_r($paypal, true)."]");
		}
		foreach($paypal as $n=>$v)
		{
			$part1 = $n;
			$this->LogMsg("PRINTR  N=[".print_r($part1, true)."]");
			$this->LogMsg("PRINTR  V=[".print_r($v, true)."]");
			foreach($v as $k=>$y)
			{
				$part2 = $k;
				$this->LogMsg("PRINTR  K=[".print_r($part2, true)."]");
				$this->LogMsg("PRINTR  Y=[".print_r($y, true)."]");
			}
			$str = $part1.$part2."]}}}";
			$this->LogMsg("String =[_____".$str."____]");
			$json = json_decode($str,true,512);
			$this->LogMsg("JSON VARDUMP [".print_r($json, true)."]");
		}
		$data = array("S"=>"OK");
		return json_encode($data);
	}



	public function dumpajax()
	{
		return $this->RecordPaypalData();



		if(Request::ajax())
		{
			$data_in = Input::all();
			$this->LogMsg("Data IN [".print_r($data_in, true)."]");
		}
		$data = array("S"=>"OK");
		return json_encode($data);
	}
}

