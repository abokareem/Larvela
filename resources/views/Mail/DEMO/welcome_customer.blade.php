@extends('Mail.RD.template-2018-04')
@section("pre-header")
	<link href='http://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
@endsection
@section("content")
<p style="text-align:center;font-family: 'Open Sans', sans-serif">
	<br>
	<span style="font-weight:bold;font-size:28px;">Welcome to {{$store->store_name }}!</span>
	<br/>
	<br/>
	<p style="text-align:left;font-family: 'Open Sans', sans-serif; font-size:17px;">Thanks for visiting our online store and subscribing, this year we are planning many new product releases so make sure you visit back often and tell your friends!<br/>
	We are also on Facebook and usually announce products there first, so "Like" our FB page and enable the notifications to get updates.<br/>
	When you visit our online store make sure you login to refresh anything in your cart or wish list and as the site is dynamic, we are constantly adding new features and functions to it.<br>
	</p>
	<p>You will from time to time receive emails from us, so can you please:
	<ul>
		<li>Add our store email to your contacts, that way you wont miss any deals, order info or product releases.</li>
		<li>Change your password, we do send a failed password notification when someone attempts to log in as you so if you receive one, be proactive and login and change it.</li>
	</ul>
	<br/>
	<br/>
	</p>
</p>
@endsection
