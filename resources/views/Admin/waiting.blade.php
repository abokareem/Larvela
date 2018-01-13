<?php namespace App;

use App\Models\Stores;
use App\Models\SubscriptionRequest;


use App\Jobs\ConfirmSubscription;

$Stores = new Stores;
$SR= new SubscriptionRequest;

$waiting = $SR->getByStatus("W");
echo "Waiting records: [".sizeof($waiting)."]<br>";

$store_code=getenv("STORE_CODE");

$store = $Stores->getByCode( $store_code );


foreach($waiting as $record)
{
	$cmd = new ConfirmSubscription($store, $record->sr_email);
	dd($cmd);
	dispatch($cmd);
}

