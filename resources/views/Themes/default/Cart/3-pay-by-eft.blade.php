@extends($THEME_HOME."master-storefront")
<?php
#
# Customer has chosen to pay by bank EFT, so display a templated view with the bank details and EFT terms and Conditions
# 
# Vars Passed In:
# ---------------
# $user      - Logged in User from users table
# $customer  - Additional Customer specific details from customers table
# $address   - Customer address from customer_addresses table
# $cart      - the row from the carts table for this user
# $store     - the current store 
# $items     - the cart item data from the carts_items table
# $postage   - the postage product being used postage method is a "product" in the products table with the combinecode set to match the shipping calculator module.
# $order     - the order table row
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
			{!! App\Helpers\SEOHelper::getText("eft_header") !!}
		</div>
	</div>

	<div class="row form-horizontal" style="font-size:18px;">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<h3>Deposit Details</h3>
			<table>
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
			</table>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<h3>Bank Details</h3>
			<table>
				<tr>
					<td style="text-align:right;">Account#:</td>
					<td width="10px">&nbsp;</td>
					<td>120-793-658</td>
				</tr>
				<tr>
					<td style="text-align:right;">BSB:</td>
					<td width="10px">&nbsp;</td>
					<td>084-004</td>
				</tr>
				<tr>
					<td style="text-align:right;">Reference:</td>
					<td width="10px">&nbsp;</td>
					<td>CID-{{$customer->id}}</td>
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
	</div>


	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("eft_payment") !!}
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("eft_button_msg") !!}
		</div>
	</div>

	<div class="row">
		<div class="text-center">
			<a href="/"><button type="button" class="btn btn-default"><span class="fa fa-shopping-cart"></span> Continue Shopping </button></a>&nbsp;&nbsp;
			<button type="button" id="btnaccept" class="btn btn-success"><span class="fa fa-shopping-cart"></span> Place Order </button>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("eft_footer") !!}
		</div>
	</div>

	<br/>
	<br/>
</div>
<form name="eft" id="eft" method="post" action="/purchased">
{!! Form::token() !!}
<input type="hidden" name="cid" value="{{$cart->id}}">
<input type="hidden" name="s" value="{{$shipping->id}}">
<input type="hidden" name="p" value="EFT">
</form>

<script>
$('#btnaccept').click(function() { $('#eft').submit(); });
</script>

@endsection
