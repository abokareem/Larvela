@extends('admin-master')
@section('title','Add New Product')
@section('content')

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>


<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header">Add New Product</h3></div>
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
			<div class="col-xs-2">
				<input type="text" class="form-control" id='prod_sku' name="prod_sku" value=''>
		 	</div>
			<label class="control-label col-xs-1">Title:</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" id='prod_title' name="prod_title" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Short Description:</label>
			<div class="col-xs-10">
				<textarea class="form-control" id='prod_short_desc' name="prod_short_desc"></textarea>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Long Description:</label>
			<div class="col-xs-10">
				<textarea class="form-control" id='prod_long_desc' name="prod_long_desc"></textarea>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Product Type:</label>
			<div class="col-xs-4">
				{!! $prod_type_select_list !!}
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Retail Cost:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id='prod_retail_cost' name="prod_retail_cost" value=''>
		 	</div>
			<label class="control-label col-xs-2">Base Cost:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id='prod_base_cost' name="prod_base_cost" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Qty:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id='prod_qty' name="prod_qty" value=''>
		 	</div>
			<label class="control-label col-xs-2">Reorder Qty:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id='prod_reorder_qty' name="prod_reorder_qty" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Weight (grams):</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id='prod_weight' name="prod_weight" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Combine Code:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id='prod_combine_code' name="prod_combine_code" value=''>
		 	</div>
		</div>
		<div class="control-group">
			<label class='control-label col-xs-2'>Visible:</label>
			<div class='col-xs-8'>
			<input type='radio' name='prod_visible' value='Y' checked> YES<br>
			<input type='radio' name='prod_visible' value='N'> NO<br>
			</div>
		</div>
		<div class="control-group">
			<label class='control-label col-xs-2'>Free Shipping:</label>
			<div class='col-xs-8'>
			<input type='radio' name='prod_has_free_shipping' value='Y'> YES<br>
			<input type='radio' name='prod_has_free_shipping' value='N' checked> NO<br>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Valid From:</label>
			<div class="col-xs-3">
				<div class='input-group date' id='datetimepicker1'>
					<input type='text' class="form-control"  name='prod_date_valid_from' value='0000-00-00'>
					<span class="input-group-addon">
						<span class="fa fa-calendar"></span>
					</span>
				</div>
			</div>
			<label class="control-label col-xs-2">Valid To:</label>
			<div class="col-xs-3">
				<div class='input-group date' id='datetimepicker2'>
					<input type='text' class="form-control"  name='prod_date_valid_to' value='0000-00-00'>
					<span class="input-group-addon">
						<span class="fa fa-calendar"></span>
					</span>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Categories:</label>
			<div class="col-xs-10 checkbox">
			@foreach($categories as $c)
			<label> <input type="checkbox" name="categories[]" value="{{ $c->id }}"> {{$c->category_title }} </label><br>
			@endforeach
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Main Product Image:</label>
			<div class="col-xs-10">
				<input name="file" type="file" id="file">
				<button id='btnclear' type="button" class="btn btn-warning">Clear Image Selection</button>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Add Product</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>


<script>
$(function() { $('#datetimepicker1').datetimepicker({ format: 'YYYY-MM-DD', showTodayButton: true }); });
$(function() { $('#datetimepicker2').datetimepicker({ format: 'YYYY-MM-DD', showTodayButton: true }); });
</script>

<script src='http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>

<script>

$('#edit').validate(
{
	rules:
	{
		prod_sku: { required: true, minlength: 3 },
		prod_title: { required: true, minlength: 3 },
		prod_weight: { required: true, minlength: 2 },
		prod_short_desc: { required: true, minlength: 7 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/product/save');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/products';
	window.location.href = url;
});
</script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>tinymce.init({ selector:'textarea' });</script>
@stop