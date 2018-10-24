<?php
/**
 * \class	CartLocking
 * \date	2018-10-24
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version 1.0.0
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
 */
namespace App\Http\Controllers\Cart;



use App\Http\Controllers\Controller;
use App\Models\ProductLock;
use App\Traits\Logger;


/** 
 * \brief  The CartLocking controller manages updating the lock times for the users cart.
 * - Products are "Lockied" at the "Confirm" stage.
 * - Locking is used to ensure the products do not exipre out and get returned to stock.
 */
class CartLocking extends Controller
{
use Logger;


	/**
	 * Constuct a new cart and make sure we are authenticated before using it.
	 *
	 * @return	void
	 */ 
	public function __construct()
	{
		$this->setFileName("larvela");
		$this->setClassName("CartLocking");
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
	 * Given the cart ID via an ajax call, update the time stamps and return OK
	 *
	 * @param	integer	$id		Cart ID to update
	 * @return	array
	 */
	public function UpdateLocks($id)
	{
		$this->LogFunction("UpdateLocks( $id )");
		$time = time();
		if(Request::ajax())
		{
			$this->LogMsg("Cart to update [".$id."]");
			$o = ProductLock::where('product_lock_cid',$id)->first();
			$o->product_lock_utime = $time;
			$cnt = $o->save();
			$data = array("S"=>"OK","C"=>$cnt);
	        return json_encode($data);
		}
		else
		{
			$data = array("S"=>"ERROR", "C"=>0);
	        return json_encode($data);
		}
	}
}
