@extends('Templates.admin-master')
@section('title','Copy Product')
@section('content')

<div class='container-fluid'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Copy Product</h3></div>
	</div>

	<form class='form-horizontal' name='edit' id='edit' method='post' enctype='multipart/form-data'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">New SKU:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id='prod_sku' name="prod_sku" value='{{ $product->prod_sku }}'>
		 	</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Duplicate Product</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	<input type='hidden' name='id' value='{{ $product->id }}'>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>

<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>

<script>

$('#edit').validate(
{
	rules:
	{
		prod_sku: { required: true, minlength: 3 },
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/product/copy/{{ $product->id }}');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/products';
	window.location.href = url;
});
</script>
@stop
