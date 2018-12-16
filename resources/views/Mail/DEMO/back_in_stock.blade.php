@extends('Mail.DEMO.template')
@section("pre-header")
	<link href='http://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
@endsection
@section("content")
<p style="text-align:center;font-family: 'Open Sans', sans-serif">
	<br>
	<span style="font-size:28px;">Good News!<br/>The following product is back in stock!</span>
	<br/>
	<br/>
<table width="65%">
	<tr style="text-align:center;">
		<td style="text-align:center;"><b style="font-size:24px;font-family:'Courgette';font-weigh:bold;">Product Details</b></td>
	</tr>
	<tr style="text-align:center;padding-top:25px;">
		<td style="text-align:center;">{{ $product->prod_sku }} - {{ $product->prod_title }}</td>
	</tr>
	@if($product->prod_qty == 1)
	<tr style="text-align:center;padding-top:25px;">
		<td style="text-align:center;">There is only 1 in stock so hurray and get it before its gone!</td>
	</tr>
	@endif
</table>
	<p style="font-size:18px;">We are letting you know because <b>you subscribed</b> for an update when its back.</p>
<br/>
<br/>
<br/>
</p>
@endsection
