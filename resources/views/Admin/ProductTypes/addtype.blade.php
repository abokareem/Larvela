@extends('Templates.admin-master')
@section('title','Add New Type')
@section('content')


<div class='container'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Add New Type</h3></div>
	</div>


	@if(count($errors)>0)
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


	<form class='form-horizontal' name='edit' id='edit' method='post' enctype='multipart/form-data'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-md-2">Token:</label>
			<div class="col-xs-12 col-md-2">
				<input type="text" class="form-control" id='product_type_token' name="product_type_token" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-md-2">Name:</label>
			<div class="col-xs-12 col-md-2">
				<input type="text" class="form-control" id='product_type' name="product_type" value=''>
		 	</div>
		</div>
	</div>


	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Add Type</button>
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
		product_type: { required: true, minlength: 4 },
		product_type_token: { required: true, minlength: 4 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/producttype/save');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/producttypes';
	window.location.href = url;
});
</script>
@stop
