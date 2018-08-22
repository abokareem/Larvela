@extends('admin-master')
@section('title','Add Block')
@section('content')
<link href="/css/bootstrap-switch.css" rel="stylesheet">



<div class='container-fluid'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Add SEO Block</h3></div>
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
			<label class="control-label col-xs-2">Token Name:</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" id='seo_token' name="seo_token" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">HTML:</label>
			<div class="col-xs-8">
				<textarea class="form-control" id='seo_html_data' name="seo_html_data" placeholder="html content here..."></textarea>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">HTML:</label>
			<div class="col-xs-8">
				<select name="seo_store_id" id="seo_store_id" class="form-control">
				@foreach($stores as $s)
					@if($s->id == $store->id)
						<option value="{{$s->id}}" selected>{{ $s->store_name }}</option>
					@else
						<option value="{{$s->id}}">{{ $s->store_name }}</option>
					@endif
				@endforeach
				</select>
		 	</div>
		</div>
		<div class="form-group">
			<label class='control-label col-xs-2'>Active:</label>
			<div class='col-xs-4'>
			<input class="bootstrap-switch" id="cb_status" data-on-text="Yes" data-off-text="No" data-off-color="danger" data-on-color="success" type="checkbox" name='seo_status' value='A' checked>
			</div>
		</div>
	</div>

	<br>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Save Block</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	<input type='hidden' name='seo_status' value='A'>
	<input type='hidden' name='editoronoff' value='Y'>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>


<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>
<script src='/bootstrap-switch.js'></script>

<script>
$('#cb_status').bootstrapSwitch();

$('#add').validate(
{
	rules:
	{
		seo_token: { required: true, minlength: 4 },
		seo_html_data: { required: true, minlength: 8 }
	}
});


$('#btnsave').click( function()
{
	$('#add').attr('action','/admin/seo/save');
	$('#add').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/seo';
	window.location.href = url;
});

</script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>tinymce.init({ selector:'textarea' });</script>
@stop
