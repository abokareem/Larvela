@extends($THEME_HOME."master-storefront")
@section("content")

<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<style>
.media-body { padding:15px; }
.uf { font-size:14px;font-family: 'Lato', sans-serif; }
.uv { font-size:16px;font-family: 'Lato', sans-serif; }
.radios { width: 100px; overflow: hidden; }
.radios > span { white-space: nowrap; }
</style>


<div class="row cartpage-block">
	<div class='text-right' style="padding-right:50px;">Cart
		<span class="fa fa-play"></span><b style="color:green;">Shipping</b>
		<span class="fa fa-play"></span> Confirm
		<span class="fa fa-play"></span> Payment
		<span class="fa fa-play"></span> Done!
	</div>
</div>

<form id="capture"  name="capture" method="POST">
<div class="row" style="padding:25px;">
	<div class="col-xs-12 col-md-2">&nbsp;</div>
	<div class="col-xs-12 col-md-6">
		<div class="control-group">
			<div class="col-xs-12"><h4>My Details</h4></div>
		</div>
		<div class="control-group">
			<div class="col-xs-6">Name:<br><input class="form-control" name="user_name" value="{{ $user->name }}"></div>
			<div class="col-xs-6">Mobile/Phone<br><input type="text" class="form-control" name="user_mobile" value="{{ $customer->customer_mobile }}"></div>
		</div>
		<div class="control-group">
			<div class="col-xs-12">Address:<br><input type="text" name="customer_address" class="form-control" value="{{ $address->customer_address }}"></div>
		</div>
		<div class="control-group">
			<div class="col-xs-8">Suburb/Town:<br><input type="text" class="form-control" name="customer_suburb" value="{{ $address->customer_suburb}}"></div>
			<div class="col-xs-4">Postcode:<br><input type="text" class="form-control" name="customer_postcode" value="{{ $address->customer_postcode }}"></div>
		</div>
		<div class="control-group">
			<div class="col-xs-6">State:<br><input type="text" class="form-control" name="customer_state" value="{{ $address->customer_state}}"></div>
			<div class="col-xs-6">Country:<br>
				<select name="customer_country" class="form-control">
				@foreach($countries as $country)
					@if($country->iso_code == $address->customer_country)
					<option value="{{$country->iso_code}}" selected>{{$country->country_name}}</option>
					@else
					<option value="{{$country->iso_code}}">{{$country->country_name}}</option>
					@endif
				@endforeach
				</select>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-2">&nbsp;</div>
</div>


<div class="row">
	<div class="col-xs-12 col-md-2">&nbsp;</div>
	<div class="col-xs-12 col-md-8"> 
		<div class="control-group">
			<div class="col-xs-12"><h4>Shipping Method:</h4></div>
		</div>
		<div class="control-group">
			<table class="table">
			@foreach($postal_options as $p)
			<tr>
				<td align="right">{!! $p->html !!}</td>
				<td align="left"><b>${{ number_format($p->cost,2)}}</b></td>
				<td><span style="color:#696969">{{$p->display}}</span></td>
			</tr>
			@endforeach
			</table>
		</div>
	</div>
	<div class="col-xs-12 col-md-2">&nbsp;</div>
</div>



<div class="row">
	<div class="col-xs-12 col-md-2">&nbsp;</div>
	<div class="col-xs-12 col-md-8">
		<div class="control-group">
			<div class="col-xs-12"><h4>Payment Method:</h4></div>
		</div>
		<div class="control-group">
			<table class="table"
			@foreach($payment_options as $option)
				<tr>
					<td align="right">{!! $option->html !!}</td>
					<td>{{ $option->name}}</td>
				</tr>
			@endforeach
			</table>
		</div>
	</div>
	<div class="col-xs-12 col-md-2">&nbsp;</div>
</div>


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
</div>
<input type="hidden" name="s" id="s" value="">
<input type="hidden" name="p" id="p" value="">
<input type="hidden" name="cid" value="{{$customer->id}}">
{!! Form::token() !!}
</form>


<script>
$('#myaccount').click(function(){
	var url = "/myaccount";
	window.location.href = url;
});


var post=false;
var pay=false;
$('#capture').on('change',function()
{
var p1 = $('input[name=shipping]:checked').val();
console.log("Post Value:"+p1 );
post=true;
$('#s').val(p1);
var p2 = $('input[name=payment_options]:checked').val();
console.log("Pay Value:"+p2 );
pay=true;
$('#p').val(p2);
if((pay==true)&&(post==true)) { $('#btnconfirm').prop('disabled', false);  }
});

$('#btnconfirm').click(function()
{
$('#capture').attr('action','/confirm?rnd=<?php echo rand(10,100); ?>');
$('#capture').submit();
});
</script>

<!-- {{ $THEME_HOME }}Cart.2-shipping -->
@endsection
