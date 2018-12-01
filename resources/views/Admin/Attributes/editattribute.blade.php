@extends('Templates.admin-master')
@section('title','Edit Attribute')
@section('content')


<div class='container'>
	<div class="row">
		<div class="col-xs-12"><h3 class="page-header">Edit Attribute</h3></div>
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
			<label class="control-label col-xs-12 col-md-2">Name:</label>
			<div class="col-xs-12 col-md-8">
				<input type="text" class="form-control" id='attribute_name' name="attribute_name" value='{{$attrbiute->attribute_name}}' placeholder="Enter the attribute name that will display">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-md-2">Token:</label>
			<div class="col-xs-12 col-md-4">
				<input type="text" class="form-control" id='attribute_token' name="attribute_token" value='{{$attribute->attribute_token}}' style="text-transform:uppercase" placeholder="Enter unique token to identify this attribute.">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-md-2">Store:</label>
			<div class="col-xs-12 col-md-4">
				<select name="store_id" class="form-control" id="store_id">
				@foreach($stores as $s)
					@if($s->id == $store->id)
					<option value="{{ $s->id }}" selected>{{ $s->store_name}}</option>
					@else
					<option value="{{ $s->id }}">{{ $s->store_name}}</option>
					@endif
				@endforeach
				</select>
		 	</div>
		</div>
	</div>


	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Save Attribute</button>
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
		attribute_name: { required: true, minlength: 4 },
		attribute_token: { required: true, minlength: 4 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/attribute/update/'+{{$attribute->id}});
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/attributes';
	window.location.href = url;
});
</script>
@stop
