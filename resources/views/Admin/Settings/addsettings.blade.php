@extends('admin-master')
@section('title','Add Setting')
@section('content')

<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header">Add Setting</h3></div>
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


	<form class='form-horizontal' name='add' id='add' method='post' enctype='multipart/form-data'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Store:</label>
			<div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
				<select class="form-control" id="store_id" name="store_id">
					<option value="0">Global - All Stores</option>
					@foreach($stores as $s)
						@if($s->id == $store_id)
						<option value="{{$s->id}}" selected>{{$s->store_name}}</option>
						@else
						<option value="{{$s->id}}">{{$s->store_name}}</option>
						@endif
					@endforeach
				</select>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Setting Name:</label>
			<div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
				<input type="text" class="form-control" id='setting_name' name="setting_name">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Value:</label>
			<div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
				<input type="text" class="form-control" id='setting_value' name="setting_value">
		 	</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2"> </label>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<button id='btnsave'   type="button" class="btn btn-success">Add Setting</button>
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
		setting_name: { required: true, minlength: 3 },
		setting_value: { required: true, minlength: 1 }
	}
});


$('#btnsave').click( function()
{
	$('#add').attr('action','/admin/setting/save');
	$('#add').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/settings';
	window.location.href = url;
});
</script>
@stop
