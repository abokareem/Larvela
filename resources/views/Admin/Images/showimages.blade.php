@extends('admin-master')
@section('title','Product Images')
@section('content')


<div class='container-fluid'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Product Images</h3></div>
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
			<label class="control-label col-xs-2">SKU:</label>
			<div class="col-xs-2">{{ $product->prod_sku }}</div>
			<label class="control-label col-xs-1">Title:</label>
			<div class="col-xs-6">{{ $product->prod_title }}</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Short Description:</label>
			<div class="col-xs-10">{{ $product->prod_short_desc }}</div>
		</div>
	</div>

	@if(sizeof($images)>0)
	<div class="row">
		<label class="control-label">Images:</label>
	</div>
	<div class="row">
		<table  class="table table-hover">
			<thead>
				<th>ID</th>
				<th>Name</th>
				<th>Folder</th>
				<th>Size</th>
				<th>HxW</th>
				<th>Order</th>
				<th>Action</th>
			</thead>
			<tbody>
			@foreach($images as $image)
				<tr>
					<td>{{ $image->id }}</td>
					<td>{{ $image->image_file_name }}</td>
					<td>{{ $image->image_folder_name }}</td>
					<td>{{ $image->image_size }} Bytes</td>
					<td>{{ $image->image_height }} x {{ $image->image_width }}</td>
					<td>{{ $image->image_order }}</td>
					<td><i class="fa fa-trash"></i> <a href="/admin/image/delete/{{$image->id }}/{{ $product->id }}">Delete</a></td>
				</tr>
				@foreach($thumbnails as $t)
					@if($t->image_parent_id == $image->id)
					<tr>
					<td>{{ $t->id }}</td>
					<td>{{ $t->image_file_name }}</td>
					<td>{{ $t->image_folder_name }}</td>
					<td>{{ $t->image_size }} Bytes</td>
					<td>{{ $t->image_height }} x {{ $image->image_width }}</td>
					<td>{{ $t->image_order }}</td>
					<td></td>
					</tr>
					@endif
				@endforeach
			@endforeach
			</tbody>
		</table>
		</div>
	</div>
	@else
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Images:</label>
			<div class="col-xs-6">
				<strong>Warning!</strong> - No Images have been uploaded for this product.
			</div>
		</div>
	</div>

	@endif
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Image:</label>
			<div class="col-xs-3">
			<input name="file" type="file" id="file">
			<label class="btn btn-default btn-file">
			</label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Mapping:</label>
			<div class="col-xs-10">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Save Product</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
				<button id='btndelete' type="button" class="btn btn-danger">Delete</button>
		 	</div>
		</div>
	</div>
	<input type='hidden' name='id' value='{{ $product->id }}'>
	{!! Form::token() !!}
	{!! Form::close() !!}
	<br/>
	<br/>
	<br/>
	<br/>
</div>



<script>

$('#edit').validate(
{
	rules:
	{
		prod_sku: { required: true, minlength: 3 },
		prod_qty: { required: true },
		prod_title: { required: true, minlength: 3 },
		prod_weight: { required: true, minlength: 2 },
		prod_short_desc: { required: true, minlength: 7 },
		prod_long_desc: { required: true, minlength: 7 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/product/update/{{ $product->id }}');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/products';
	window.location.href = url;
});


$('#btndelete').click(function()
{
	$('#edit').attr('action','/admin/product/delete/{{ $product->id }}');
	$('#edit').submit();
});
</script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>tinymce.init({ selector:'textarea' });</script>
@stop
