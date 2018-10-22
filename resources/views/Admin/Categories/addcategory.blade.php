@extends('Templates.admin-master')
@section('title','Add New Category')
@section('content')

<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header">Add New Category</h3></div>
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

	<form class='form-horizontal' name='add' id='add' method='post'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Store:</label>
			<div class="col-xs-6">
				<select id="category_store_id" name="category_store_id" class="form-control">
				@foreach($stores as $s)
					<option value="{{$s->id}}">{{$s->store_name}}</option>
				@endforeach
				</select>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Title:</label>
			<div class="col-xs-12 col-sm-6">
				<input type="text" class="form-control" id='category_title' name="category_title" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Description:</label>
			<div class="col-xs-12 col-sm-6">
				<textarea class="form-control" name="category_description" id='category_description'></textarea>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">URL:</label>
			<div class="col-xs-12 col-sm-6">
				<input type="text" class="form-control" id='category_url' name="category_url" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Parent:</label>
			<div class="col-xs-12 col-sm-5">
				<select id="category_parent_id" name="category_parent_id" class="form-control">
				<option value="0">No parent</option>
				@foreach($categories as $c)
					<option value="{{$c->id}}">{{$c->category_title}}</option>
				@endforeach
				</select>
		 	</div>
		</div>
		<div class="control-group">
			<label class='control-label col-xs-12 col-sm-2'>Status:</label>
			<div class='col-xs-8'>
			<input type='radio' name='category_status' value='A' checked> Enabled<br>
			<input type='radio' name='category_status' value='C'> Disabled<br>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="control-group">
			<label class='control-label col-xs-12 col-sm-2'>Visible:</label>
			<div class='col-xs-6'>
			<input type='radio' name='category_visible' value='Y' checked> Visible on Menu<br>
			<input type='radio' name='category_visible' value='N'> Hidden<br>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Add Category</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>


<script src='https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>

<script>

$('#add').validate(
{
	rules:
	{
		category_title: { required: true, minlength: 3 },
		category_description: { required: true, minlength: 7 }
	}
});


$('#btnsave').click( function()
{
	$('#add').attr('action','/admin/category/save');
	$('#add').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/categories';
	window.location.href = url;
});
</script>
<!-- Admin.Categories.addcategory -->
@stop
