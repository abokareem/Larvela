@extends('Mail.DEMO.template')
@section("content")
<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<p style="text-align:center;font-family: 'Roboto', sans-serif;font-size:36px;">
<br/>
<span class="text-center">Order Dispatched
<br/>
</p>
<p style="text-align:center;font-family: 'Open Sans', sans-serif;font-size:16px;">
Your order <b>(Order #{!! str_pad( $order->id,8,"0", STR_PAD_LEFT) !!})</b> has been dispatched and is currently in transit to your destination.
</span>
</p>
<p style="text-align:center;font-family: 'Open+Sans', sans-serif;font-size:18px;">
<table>
	<tr valign="top">
		<td valign="top" width="33%">@IncludeIf('Mail.DEMO.block-customer-details')</td>
		<td valign="top" width="34%"> </td>
		<td valign="top" width="33%">@IncludeIf('Mail.DEMO.block-order-details')</td>
	</tr>
</table>

@IncludeIf('Mail.DEMO.block-order-items-table')
<br/>
<br/>
<br/>
</p>
@endsection
