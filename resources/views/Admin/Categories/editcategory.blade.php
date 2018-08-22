@extends('admin-master')
@section('title','Edit Category')
@section('content')

<div class='container'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Edit Category</h3></div>
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

	<form class='form-horizontal' name='edit' id='edit' method='post'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Store:</label>
			<div class="col-xs-6">
				{{ $store->store_name }}
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Title:</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" id='category_title' name="category_title" value='{{ $category->category_title }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Description:</label>
			<div class="col-xs-6">
				<textarea class="form-control" name="category_description" id='category_description'>{{ $category->category_description }}</textarea>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">URL:</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" id='category_url' name="category_url" value='{{ $category->category_url }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Parent:</label>
			<div class="col-xs-5">
				<select id="category_parent_id" name="category_parent_id" class="form-control">
				@foreach($categories as $c)
					@if($c->id == $category->category_parent_id)
					<option value="{{ $c->id}}" selected>{{$c->category_title}}</option>
					@else
					<option value="{{ $c->id}}">{{$c->category_title}}</option>
					@endif
				@endforeach
				</select>
		 	</div>
		</div>
		<div class="control-group">
			<label class='control-label col-xs-2'>Status:</label>
			<div class='col-xs-8'>
			<?php $status = $category->category_status; ?>
			<input type='radio' name='category_status' value='A' <?php echo ($status=='A')?"checked":""; ?> > Enabled<br>
			<input type='radio' name='category_status' value='C' <?php echo ($status=='C')?"checked":""; ?> > Disabled<br>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="control-group">
			<label class='control-label col-xs-2'>Visible:</label>
			<div class='col-xs-8'>
			<?php $vis = $category->category_visible; ?>
			<input type='radio' name='category_visible' value='Y' <?php echo ($vis=='Y')?"checked":""; ?> > Visible<br>
			<input type='radio' name='category_visible' value='N' <?php echo ($vis=='N')?"checked":""; ?> > Hidden<br>
			</div>
		</div>

		<input type="hidden" name="id" value="{{ $category->id }}">
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Save</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
				<button id='btndelete' type="button" class="btn btn-danger">Delete</button>
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
		category_title: { required: true, minlength: 3 },
		category_description: { required: true, minlength: 7 }
	}
});


$('#btndelete').click( function()
{
	$('#edit').attr('action','/admin/category/delete/{{ $category->id }}');
	$('#edit').submit();
});

$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/category/update/{{ $category->id }}');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/categories';
	window.location.href = url;
});
</script>
@stop
