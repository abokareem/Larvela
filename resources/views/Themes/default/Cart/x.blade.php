<?php

$a = array('{"id":"PAY-39D30085F1429091SLGZ45YI","intent":"sale","state":"approved","cart":"8V910208S8198743V","create_time":"2017-09-09T11:22:26Z","payer":{"payment_method":"paypal","status":"VERIFIED","payer_info":{"email":"sales-buyer@hs-retro-fashions_com","first_name":"test","middle_name":"test","last_name":"buyer","payer_id":"SRXW2M35DGUQC","country_code":"AU","shipping_address":{"recipient_name":"test_buyer","line1":"PO_Box_611","line2":"Ashgrove","city":"Brisbane","state":"QLD","postal_code":"4060","country_code":"AU"}}},"transactions":' => array('{"amount":{"total":"88.40","currency":"AUD","details":{"subtotal":"80.00","tax":"0.00","shipping":"8.40"}},"item_list":{"items":[{"name":"PC-DL-63-BLACK","price":"80.00","currency":"AUD","quantity":1,"description":"Black Double Layer 5 Tier Petticoat"}' => ''));

foreach($a as $n=>$v)
{
	echo "_N_=";
	var_dump($n);
	echo "_V_=";
	var_dump($v);
	echo PHP_EOL;
	echo PHP_EOL;
	foreach($v as $k=>$y)
	{
		echo "_K_=";
		var_dump($k);
		echo "_Y_=";
		var_dump($y);
		echo PHP_EOL;
		echo PHP_EOL;
	}
}

