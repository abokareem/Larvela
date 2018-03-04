@extends($THEME_HOME."master-storefront")
@section("content")
<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<style>
.media-body { padding:15px; }
.uf { font-size:14px;font-family: 'Lato', sans-serif; }
.uv { font-size:16px;font-family: 'Lato', sans-serif; }

.radios {
    width: 100px;
	    overflow: hidden;
		}
		.radios > span {
		    white-space: nowrap;
			}
</style>
	<div class="row cartpage-block">
		<div class='text-right' style="padding-right:50px;">Cart
			<span class="fa fa-play"></span><b style="color:green;"> Shipping</b>
			<span class="fa fa-play"></span> Confirm
			<span class="fa fa-play"></span> Payment
			<span class="fa fa-play"></span> Done!
		</div>
	</div>

<div class="container">
	<div class="row">
		<div class="control-group">
			<div class="col-xs-3 text-right"><h4>My Details</h4></div>
			<div class="col-xs-9"> </div>
		</div>
	</div>
	<div class="row">
		<div class="control-group">
			<div class="col-xs-3 text-right uf">Name:</div>
			<div class="col-xs-9"><b class="uv">{{ $user->name }}</b></div>
		</div>
		<div class="control-group">
			<div class="col-xs-3 text-right uf">eMail:</div>
			<div class="col-xs-9"><b class="uv">{{ $user->email }}</b></div>
		</div>
		<div class="control-group">
			<div class="col-xs-3 text-right uf">Mobile:</div>
			<div class="col-xs-9"><b class="uv">{{ $customer->customer_mobile }}</b></div>
		</div>
		<div class="control-group">
			<div class="col-xs-3 text-right uf">Address:</div>
			<div class="col-xs-9"><b class="uv">{{ $address->customer_address }}</b></div>
			<div class="col-xs-3 text-right uf">Suburb/Postcode:</div>
			<div class="col-xs-9"><b class="uv">{{ $address->customer_suburb}}, {{ $address->customer_postcode }}</b></div>
			<div class="col-xs-3 text-right uf">State:</div>
			<div class="col-xs-9"><b class="uv">{{ $address->customer_state}}</b></div>
			<div class="col-xs-3 text-right uf">Country:</div>
			<div class="col-xs-9"><b class="uv">{{ $address->customer_country}}</b></div>
		</div>
	</div>
	<div class="row">
		<div class="control-group">
			<div class="col-xs-3">&nbsp;</div>
			<div class="col-xs-9">
				<button type="button" id="myaccount" name="mydetails" class="btn btn-warning">Update My Details</button>
			</div>
		</div>
	</div>
	<br/>
	<br/>

	<div class="row">
	<form id="shipment_types"  name="shipment_types">
		<div class="col-xs-3">
			<h4 class="text-right">Postal Options</h4>
		</div>
		<div class="col-xs-9">
			@foreach($postal_options as $p)
			<span class="input-group"><input type="radio" name="post_options" value="{{$p->id}}" /> {{$p->prod_title}} - <b>${{ number_format($p->prod_retail_cost,2)}}</b></span>
			@endforeach
			<span class="input-group"><input type="radio" name="post_options" value="0"> Local Pickup - <b>$0.00</b> Brisbane Only!</span>
		</div>
	</form>
	</div>
	<br/>
	<br/>

	<div class="row">
	<form id="payment_types"  name="payment_types">
		<div class="col-xs-3">
			<h4 class="text-right">Payment Options</h4>
		</div>
		<div class="col-xs-9">
			<span class="input-group"><input type="radio" name="payment_options" value="0"> Cash On Delivery</span>
			<span class="input-group"><input type="radio" id="btnbank" name="payment_options" value="BD"> Bank Deposit</span>
			<span class="input-group"><input type="radio" id="btncc" name="payment_options" value="CC" disabled> Credit Card</span>
			<span class="input-group">
				<input type="radio" id="btnpp" name="payment_options" value="PP"> Paypal / Credit Card via Paypal
			</span>
		</div>
	</form>
	</div>
	<br/>
	<br/>
	<div class="row">
		<div class="text-center">
		<a href="/"><button type="button" class="btn btn-default"><span class="fa fa-shopping-cart"></span> Continue Shopping </button></a>&nbsp;&nbsp;
		<button id='btnconfirm' type="button" class="btn btn-success" disabled> Confirm <span class="fa fa-play"></span></button>
		</div>
	</div>
	<br/>
	<br/>
	<div class="row">
		<!-- div class="text-center"><b style='font-size:18px;color:red;'>Payment Gateway Down</b></div -->
	</div>
	<br/>
	<br/>
</div>
<form id="capture" method="POST">
{!! Form::token() !!}
<input type="hidden" name="s" id="s" value="">
<input type="hidden" name="p" id="p" value="">
<input type="hidden" name="cid" value="{{$customer->id}}">
</form>


<script>
$('#myaccount').click(function(){
	var url = "/myaccount";
	window.location.href = url;
});


var post=false;
var pay=false;
$('#shipment_types').on('change',function()
{
var p1 = $('input[name=post_options]:checked').val();
console.log("Post Value:"+p1 );
post=true;
$('#s').val(p1);
if((pay==true)&&(post==true)) { $('#btnconfirm').prop('disabled', false); }
});

$('#payment_types').on('change',function()
{
var p2 = $('input[name=payment_options]:checked').val();
console.log("Pay Value:"+p2 );
pay=true;
$('#p').val(p2);
if((pay==true)&&(post==true)) { $('#btnconfirm').prop('disabled', false);  }
});

$('#btnconfirm').click(function()
{
$('#capture').attr('action','/confirm');
$('#capture').submit();
});
</script>

<!-- {{ $THEME_HOME }}.Cart.2-shipping -->
@endsection
