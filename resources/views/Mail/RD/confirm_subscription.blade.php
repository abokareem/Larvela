@extends('Mail.RD.template-2018-04')
@section("pre-header")
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
@endsection
@section("content")
<center>
<table style="background-color:white;">
<tr>
<td width="100%" style="vertical-align:top;">
<center>
<h3>Subscription Confirmation Required!</h3>
<p style="text-align:center;font-family: 'Dancing Script', cursive;font-size:24px;">Thank you for visiting our web site!</p>
<p style="text-align:center;">We would however like to confirm your subscription as we often get people entering other peoples email addresses for reasons only known to them.</P>
<p style="text-align:center;">To confirm your subscription you can either cut and paste the web site link below to get directed to our web site or click on the link below.</p>
</center>
</td></tr></table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
<p style="text-align:center;">
<a href="https://rockabillydames.com/confirmed/{$hash}}"> Confirm Subscription </a><br><br>OR<br><br>
Cut and paste the link below:<br><br>
https://rockabillydames.com/confirmed/{{$hash}}</br></p>
<p style="text-align:center;">If you did <b>NOT</b> request a subscription, then just delete this email and in 72 hours the subscription request will be deleted permanently.</p>
<p style="text-align:center;">Please note that another request will be sent in 24hrs if this subscription request is not answered, you can delete it as well.</p>
</center>
</td>
</tr>
</table>
</center>
@endsection
