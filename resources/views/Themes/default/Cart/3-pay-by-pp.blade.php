@extends($THEME_HOME."master-storefront")
<?php
#
# Customer has chosen to pay by Paypal, so display a templated view with the paypal express code.
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
<script src="//www.paypalobjects.com/api/checkout.js"></script>
<style>
.media-body { padding:15px; }
</style>
<div class="container">
	<div class="row">
		<div class="col-xs-12">{!! App\Helpers\SEOHelper::getText("paypal_payment_header") !!}</div>
	</div>

	<div class="row form-horizontal" style="font-size:18px;">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<h3>Payment Details</h3>
			<table class="table">
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
		
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<br/><br/><p style="text-align:center;font-family: 'Open Sans';font-size:16px;">
				To make a payment via paypal,<br/>please select the "Paypal Checkout" button below:<br/><br/>
				<span class="text-center" id='paypal-button'></span>
			</div>
		</div>

		<div class="row">
			<span id="paymentstatus" style="visibility:hidden;">Status: <span id='paypal-button-container'> - </span></span>
		</div>

		<div class="row">
			<div class="text-center">
				<a href="/"><button type="button" id="btncs" class="btn btn-default"><span class="fa fa-shopping-cart"></span> Continue Shopping </button></a>&nbsp;&nbsp;
			</div>
		</div>
		<br/>
		<br/>
	</div>
</div>

<!-- sandbox: 'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R', -->

<?php
$token = csrf_token();
$cnt = sizeof($products);
?>

<script>

paypal.Button.render({
	env:'sandbox', commit:true, 
	client: {
		sandbox: 'AUirUBG6ImLCuDCocB3FXbU9ufAsW3xSQlCxgx0T-fthAPl4_o8t4CVqas5iP-5DuX3Vxbt3V88FiXCi',
		production: 'AbmINr3QL340DWkPf6WjKJaEKKbOKiTCd3roAcR4u0sqTs2q6fcFGqw7nE4J5t-61DTUXBd3bCZ4d4Jr'
		},
	payment:function(data, actions)
	{
		console.log("payment()");
		return actions.payment.create(
		{
		payment:
		{
			transactions:[{ amount: { total:'{{ number_format($cart_data->cd_total,2) }}', currency:'AUD', details:{ 'subtotal':'{{ number_format($cart_data->cd_sub_total,2)}}', 'tax':'{{ number_format($cart_data->cd_tax,2) }}', 'shipping':'{{ number_format($cart_data->cd_shipping,2) }}', }
				},
				item_list:
				{ items:[
@foreach($items as $cartitem) @foreach($products as $p) @if($p->id == $cartitem->product_id) { name: '{{ $p->prod_sku }}', description: '{{ $p->prod_short_desc}}', quantity: '{{ $cartitem->qty}}', price: '{{ number_format($p->prod_retail_cost,2) }}', currency: 'AUD' } <?php $cnt--; ?> @if($cnt>0) , @endif @endif @endforeach @endforeach
	]
				}
			}]
		}});
	},
	onAuthorize:function(data, actions)
	{
		console.log("onAuthorize()");
		console.log("Dumping DATA");
		console.log(data);
		console.log("Dumping ACTIONS");
		console.log(actions);
		return actions.payment.execute().then(function(payment)
		{
			clearTimeout(lockTimer);
			console.log("payment.execute called");
			document.querySelector('#paypal-button').innerText = 'Payment Complete!';
			console.log("Dumping payment:");
			console.log("CART: "+payment['cart']);
			console.log(payment);
			var values = encodeURIComponent(JSON.stringify(payment));
			$.ajaxSetup({headers:{'X-CSRF-TOKEN':'{{ $token }}' } } );
			ajaxRequest= $.ajax({ url: "/ajax/payment", type: "post",data:values });
			ajaxRequest.done(function(response, textStatus, jqXHR)
			{
				console.log("PRE DUMP = "+response);
				var result = $.parseJSON(response);
				console.log("POST DUMP = "+result);
			});
			ajaxRequest= $.ajax({ url: "/cart/order/{{$cart->id}}", type: "post",data:values });
			ajaxRequest.done(function(response, textStatus, jqXHR)
			{
				$('#btncs').removeClass('btn-default');
				$('#btncs').addClass('btn-success');
				$('#btncs').text('OK');
			});
		});
	},
	onCancel: function(data,actions)
	{
		console.log("Cancel called");
		console.log("Dumping DATA");
		console.log(data);
		console.log("Dumping ACTIONS");
		console.log(actions);
	}
}, '#paypal-button');


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
var ALLOWEDTIME = 3;
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
