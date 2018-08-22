@extends('admin-master')
@section('title','Add New Customer')
@section('content')

<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header"><i class='fa fa-user-plus'></i> Add New Customer</h3></div>
	</div>

	<form class='form-horizontal' name='edit' id='edit' method='post'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Name:</label>
			<div class="col-xs-5">
				<input type="text" class="form-control" name="customer_name" id='customer_name'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">eMail Address:</label>
			<div class="col-xs-5">
				<input type="text" class="form-control" id='customer_email' name="customer_email" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Mobile:</label>
			<div class="col-xs-3">
				<input type="text" class="form-control" id='customer_mobile' name="customer_mobile" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Source:</label>
			<div class="col-xs-5">
				<select class="form-control" id="customer_source_id" name="customer_source_id">
					@foreach($sources as $s)
						<option value="{{$s->id}}">{{$s->cs_name}}</option>
					@endforeach
				</select>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Store:</label>
			<div class="col-xs-5">
				<select class="form-control" id="customer_store_id" name="customer_store_id">
					<option value="0">Global - All Stores</option>
					@foreach($stores as $s)
						@if($s->id == $store->id)
							<option value="{{$s->id}}" selected>{{$s->store_name}}</option>
						@else
							<option value="{{$s->id}}">{{$s->store_name}}</option>
						@endif
					@endforeach
				</select>
		 	</div>

		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Status:</label>
			<div class='col-xs-8'>
				<input type='radio' name='customer_status' value='A' checked> Enabled<br>
				<input type='radio' name='customer_status' value='X'> Disabled<br>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Add Customer</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>


<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>

<script>

$('#edit').validate(
{
	rules:
	{
		customer_name: { required: true, minlength: 3 },
		customer_email: { required: true, minlength: 3 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/customer/save');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/customers';
	window.location.href = url;
});
</script>
@stop
