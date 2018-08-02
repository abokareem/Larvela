@extends('Mail.RD.template-2018-04')
@section("content")
<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<p style="text-align:center;font-family: 'Roboto', sans-serif;font-size:36px;">
<br/>
<span class="text-center">
<br/>
</p>
<p style="text-align:center;font-family: 'Open Sans', sans-serif;font-size:16px;">
<br/><br/>
<span style="font-weight:bold;font-size:18px;">Hi {{ strtok($customer->customer_name," ") }}!</span><br/>
<br/>
Thanks for visiting the {{$store->store_name}} online store last week, we noticed you abandoned your shopping cart and havn't been back?<br/>We would love to see you back and complete your order.<br/>
<br/>
If you have any inquiries don't hesitate to contact me on <b>{{$store->store_contact}}</b><br/>
<br/>
If you want to email me then add {{ $store->store_sales_email}} to your contact list and I will respond promptly!<br/>
<br/>
<br/>
@IncludeIf('Mail.RD.block-cart-items-table')
<br/>
<br/>
Hopefully you will be back so I will keep the items in the cart for you for the next few weeks.<br/>
<br/>
<br/>
<span style="font-size:14px;">Sales and Marketing</span><br/>
<span style="font-size:24px;font-weight:bold;">{{$store->store_name}}</span><br/>
<br/>
<br/>
</p>
@endsection
