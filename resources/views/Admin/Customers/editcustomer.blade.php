@extends('Templates.admin-master')
@section('title','Edit New Customer')
@section('content')
<link href="/css/bootstrap-switch.css" rel="stylesheet">

<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header"><i class='fa fa-user'></i> Edit Customer</h3></div>
	</div>

	@if(count($errors)>0 )
	<div class="row">
		<div class="alert alert-danger col-xs-4">
			<ul>
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	</div>
	@endif

	<form class='form-horizontal' name='edit' id='edit' method='post'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Name:</label>
			<div class="col-xs-5">
				<input type="text" class="form-control" name="customer_name" id='customer_name' value='{{ $customer->customer_name }}'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">eMail Address:</label>
			<div class="col-xs-5">
				<input type="text" class="form-control" id='customer_email' name="customer_email" value='{{ $customer->customer_email }}'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Mobile:</label>
			<div class="col-xs-3">
				<input type="text" class="form-control" id='customer_mobile' name="customer_mobile" value='{{ $customer->customer_mobile }}'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Source:</label>
			<div class="col-xs-5">
				<select class="form-control" name="customer_source_id" id="customer_source_id">
				@foreach($customer_sources as $cs)
					@if($customer->customer_source_id == $cs->id)
					<option value="{{$cs->id}}" selected>{{ $cs->cs_name }}</option>
					@else
					<option value="{{$cs->id}}">{{ $cs->cs_name }}</option>
					@endif
				@endforeach
				</select>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Store:</label>
			<div class="col-xs-5">
				<select class="form-control" name="customer_store_id" id="customer_store_id">
				@foreach($stores as $s)
					@if($customer->customer_store_id == $s->id)
					<option value="{{$s->id}}" selected>{{ $s->store_name }}</option>
					@else
					<option value="{{$s->id}}">{{ $s->store_name }}</option>
					@endif
				@endforeach
				</select>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Status:</label>
			<div class='col-xs-8'>
				<?php $chk=""; if($customer->customer_status=="A") $chk=" checked "; ?>
				<input class="bootstrap-switch" id="cb_status" data-on-text="Active" data-off-color="danger" data-on-color="success" type="checkbox" name='cb_status' {!! $chk !!}>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Update Customer</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	<input type="hidden" value="{{ $customer->customer_status }}" name="customer_status">
	<input type="hidden" value="{{ $customer->id }}" name="id">
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>


<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>
<script src='/bootstrap-switch.js'></script>



<script>
$('#cb_status').bootstrapSwitch();

$('input[name="cb_status"]').on('switchChange.bootstrapSwitch', function(event, state)
{
	var v = (state==true) ? 'A' : 'N';
	console.log("S="+state+"  V="+v);
	$("input[name='customer_status']").val(v);
});


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
	$('#edit').attr('action','/admin/customer/update/{{ $customer->id }}');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/customers';
	window.location.href = url;
});
</script>
@stop
