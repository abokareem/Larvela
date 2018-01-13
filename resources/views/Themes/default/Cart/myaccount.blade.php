@extends($THEME_HOME."master-storefront")
@section("content")


<div class="container">
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">My Account</h3></div>
	</div>
	<form id="update" class="form-horizontal" method="POST">
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Name:</label>
			<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">{{ $customer->customer_name }}</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Email:</label>
			<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">{{ $customer->customer_email }}</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Mobile #:</label>
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<input type="text" name="customer_mobile" id="customer_mobile" value="{{$customer->customer_mobile}}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Creation Date:</label>
			<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">{{ $customer->customer_date_created }}</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12"><h4 class="page-header">Postal Address details</h4></div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Postal Address:</label>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<input type="text" name="customer_address" id="customer_address" value="{{ $address->customer_address}}" size="62" class="form-control">
			</div>	
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Suburb:</label>
			<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
				<input type="text" name="customer_suburb" id="customer_suburb" value="{{ $address->customer_suburb}}" size="32" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Postcode/Zip:</label>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<input type="text" name="customer_postcode" id="customer_postcode" value="{{ $address->customer_postcode}}" size="6" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">City:</label>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<input type="text" name="customer_city" id="customer_city" value="{{$address->customer_city}}" size="62" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">State:</label>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<input type="text" name="customer_state" id="customer_state" value="{{$address->customer_state }}" size="62" class="form-control">
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Country:</label>
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<select name="customer_country" id="customer_country" class="form-control">
					<option value="AU" selected>Australia</option>
					<option value="NZ">New Zealand</option>
				</select>
			</div>
		</div>
	</div>
	<input type="hidden" name="cid" id="cid" value="{{$customer->id}}">
	{!! Form::token() !!}
	</form>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2"> </label>
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<button type="button" class="btn btn-success" id="btnupdate">Update my Details</button>
				<button type="button" class="btn btn-warning" id="btncancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>

$('#btncancel').click(function()
{
var url='/home'
window.location.href=url;
});

$("#btnupdate").click(function()
{
console.log("update!");
$("#update").attr("action","/myaccount/update/{{ $customer->id }}");
$("#update").submit();
event.preventDefault();
});
</script>
<!-- END:Frontend.Cart.myaccount -->
@stop
