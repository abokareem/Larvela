<?php

$Adverts = new \App\Adverts();

$data = array('advert_name'=>'web page shipping promo','advert_html_code'=>'<div class=\"panel\"><h1>Advert here</h1></div>','advert_status'=>'A','advert_date_from'=>'2016-05-12','advert_date_to'=>'2016-05-12','advert_store_id'=>0);
	$rv = $Adverts->InsertAdvert($data);
	echo "Insert returned [".$rv."]<br>";
