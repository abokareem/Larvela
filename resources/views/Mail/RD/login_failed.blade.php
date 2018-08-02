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
<span style="font-weight:bold;font-size:18px;">Hi {{ strtok($customer->customer_name," ") }}</span><br/>
<br/>
This email is to inform you that a <span style="color:red;font-weight:bold">failed login</span> attempt has been recorded using your email address at our store.
<br/>
If you have any inquiries don't hesitate to contact me on <b>{{$store->store_contact}}</b><br/>
<br/>
If you want to email me then add {{ $store->store_sales_email}} to your contact list and I will respond promptly!<br/>
<br/>
<br/>
<br/>
Hopefully it was you who performed the failed login and not someone trying to break into your account!
<br/>
<br/>
<span style="font-size:14px;">Security and Engineering</span><br/>
<span style="font-size:24px;font-weight:bold;">{{$store->store_name}}</span><br/>
<br/>
<br/>
</p>
@endsection
