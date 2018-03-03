<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\QueueTest;
use App\Jobs\MailRun;
use App\Jobs\CleanupOrphanImages;


class TestController extends Controller
{
	public function cleanupimages()
	{
		dispatch(new CleanupOrphanImages());
	}





	public function mailrun()
	{
		$store=app('store');
		#$email = "sid.young@gmail.com";
		$email = "janelle.blavius@gmail.com";
		$subject = "TEST - Valentines day - sorted!";
		$filename = "mail-run";

		dispatch(new MailRun($store,$email,$subject,$filename));
	}




	public function qt()
	{
		dispatch(new QueueTest());
	}
}
