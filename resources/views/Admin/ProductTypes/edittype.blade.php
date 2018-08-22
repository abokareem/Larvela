@extends('admin-master')
@section('title','Edit Product Type')
@section('content')


<div class='container'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Edit Product Type</h3></div>
	</div>


	<form class='form-horizontal' name='edit' id='edit' method='post' enctype='multipart/form-data'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Name:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id='product_type' name="product_type" value="{{ $product_type->product_type}}">
		 	</div>
		</div>
	</div>


	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Save Type</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	<input type="hidden" name="id" value="{{ $product_type->id}}">
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>

<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>

<script>

$('#edit').validate(
{
	rules:
	{
		product_type: { required: true, minlength: 3 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/producttype/update/{{ $product_type->id}}');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/producttypes';
	window.location.href = url;
});
</script>
@stop
