@extends($THEME_HOME."master-storefront")
<?php
#
# Customer has chosen to pick up locally
# 
# Vars Passed In:
# ---------------
# $user      - Logged in User from users table
# $customer  - Additional Customer specific details from customers table
# $address   - Customer address from customer_addresses table
# $cart      - the row from the carts table for this user
# $items     - the cart item data from the carts_items table
# $postage   - the postage product being used postage method is a "product" in the products table with the combinecode set to match the shipping calculator module.
#
# Todo List:
# ----------
# Return a formatted list of products not items that match the display data in the cart.
#
#
#?>
@section('content')
<style>
.media-body { padding:15px; }
table {border-collapse: separate; border-spacing:0; }
td { position: relative; padding: 1px; }
td { right: 10px; }
td:first-child { right: 10px; }
</style>


<div class="container">
	<div class="row cartpage-block">
		<div class='text-right' style="padding-right:50px;"> Cart
			<span class="fa fa-play"></span> Shipping
			<span class="fa fa-play"></span> <b style="color:green;"> Confirm</b>
			<span class="fa fa-play"></span> Payment
			<span class="fa fa-play"></span> Done
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("local_pickup_header") !!}
		</div>
	</div>

	<div class="row form-horizontal" style="font-size:18px;">
		@if($cart_data->cd_payment_method == "BD")
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		@else
		<div class="col-xs-12">
		@endif
			<h3>Order Details</h3>
			<table class="table text-centered table-borderless">
				<tr>
					<td style="text-align:right;">Sub Total:</td>
					<td style="text-align:right;">${{ number_format($cart_data->cd_sub_total,2) }}<td>
				</tr>
				<tr>
					<td style="text-align:right;">Shipping:</td>
					<td style="text-align:right;" style="text-align:right;">${{ number_format($cart_data->cd_shipping,2) }}</td>
				</tr>
				<tr>
					<td style="text-align:right;">Tax:</td>
					<td style="text-align:right;">${{ number_format($cart_data->cd_tax,2) }}</td>
				</tr>
				<tr style="font-weight:bold;color:green;">
					<td style="text-align:right;">Total:</td>
					<td style="text-align:right;">${{ number_format($cart_data->cd_total,2) }}</td>
				</tr>
				@if($cart_data->cd_payment_method != "BD")
				<tr style="font-weight:bold;color:blue;">
					<td style="text-align:right;">Order Ref#:</td>
					<td style="text-align:right;">{{ $order->order_number }}</td>
				</tr>
				@endif
			</table>
		</div>
		@if($cart_data->cd_payment_method == "BD")
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<h3>Bank Details</h3>
			<table  class="table text-centered table-borderless">
				<tr>
					<td style="text-align:right;">Account#:</td>
					<td width="10px">&nbsp;</td>
					<td>000-000-000</td>
				</tr>
				<tr>
					<td style="text-align:right;">BSB:</td>
					<td width="10px">&nbsp;</td>
					<td>000-000</td>
				</tr>
				<tr>
					<td style="text-align:right;">Reference:</td>
					<td width="10px">&nbsp;</td>
					<td>{{ $order->order_number}}</td>
				</tr>
				<tr>
					<td style="text-align:right;">Account Name:</td>
					<td width="10px">&nbsp;</td>
					<td>Present and Future Holdings Pty Ltd</td>
				</tr>
				<tr>
					<td style="text-align:right;">Bank:</td>
					<td width="10px">&nbsp;</td>
					<td>National Australia Bank</td>
				</tr>
			</table>
		</div>
		@endif
	</div>

	@if($cart_data->cd_payment_method == "BD")
	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("local_pickup_details") !!}
		</div>
	</div>
	@endif

	<div class="row">
		<br/>
		<br/>
		<p class='text-center'><b>
		Please Confirm your order below by selecting Accept and contact us on the number below.</b></p>
		<br/>
	</div>

	<div class="row">
		<div class="text-center">
			<a href="/"><button type="button" class="btn btn-default"><span class="fa fa-shopping-cart"></span> Continue Shopping </button></a>&nbsp;&nbsp;
			<button type="button" id="btnaccept" class="btn btn-success"><span class="fa fa-check"></span> Place Order </button>
		</div>
	</div>
	<form name="cod" id="cod" method="POST" action="/purchased">
	<input type="hidden" name="cid" value="{{ $cart->id}}">
	<input type="hidden" name="p" value="COD">
	<input type="hidden" name="s" value="LOCAL">
	<input type="hidden" name="orderref" value="{{ $order->order_number }}">
	{!! Form::token() !!}
	</form>
	<br/>
	<br/>
</div>
<script>
$('#btnaccept').click(function()
{
	$('#cod').submit();
// add accept POST back here so order generation occurs and emails dispatched.
});

<?php $token = csrf_token(); ?>

<?php
#
# While we are on the confirm page, we need to maintain the product locks,
# call an AJAX method to update the product lock timestamps.
# When we stop updating the lock, a background task will clear them after 5 minutes and
# return locked stock to
#
# Time runs every 60 seconds
#?>
var POLLTIMER = 60000;
var ALLOWEDTIME = 5;
var timeonpage = 0;
function UpdateLocks()
{
	if(timeonpage < ALLOWEDTIME)
	{
		console.log("Update Locks - "+timeonpage);
		timeonpage++;
		$.ajaxSetup({headers:{'X-CSRF-TOKEN':'{{ $token }}' } } );
		ajaxRequest= $.ajax({ url: "/ajax/updatelocks/{{$cart->id}}", type: "post" });
		ajaxRequest.done(function(response, textStatus, jqXHR)
		{
			var result = $.parseJSON(response);
			console.log(result.S);
		});
		lockTimer = setTimeout(UpdateLocks,POLLTIMER);
	}
	else
	{
		var url = "/cart/error/cart-timeout";
		window.location.href = url;
	}
}



var lockTimer = setTimeout(UpdateLocks,POLLTIMER);
</script>

@endsection
