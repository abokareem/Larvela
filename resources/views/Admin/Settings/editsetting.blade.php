@extends('admin-master')
@section('title','Edit Setting')
@section('content')

<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header">Edit Setting</h3></div>
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
			<label class="control-label col-xs-2">Store:</label>
			<div class="col-xs-4">
				{!! $store_select_list !!}
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Setting Name:</label>
			<div class="col-xs-7">
				<input type="text" class="form-control" id='setting_name' name="setting_name" value="{{ $setting->setting_name }}">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Value:</label>
			<div class="col-xs-7">
				<input type="text" class="form-control" id='setting_value' name="setting_value" value="{{ $setting->setting_value }}">
		 	</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Save Setting</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>

<script src='http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>

<script>

$('#edit').validate(
{
	rules:
	{
		setting_name: { required: true, minlength: 3 },
		setting_value: { required: true, minlength: 1 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/setting/save/{{ $setting->id }}');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/settings';
	window.location.href = url;
});
</script>
@stop
