@extends('Templates.admin-master')
@section('title','Add Basic Product')
@section('content')

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>


<div class='container'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Add a new BASIC Product</h3></div>
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
	<ul id='tabs' class='nav nav-tabs' data-tabs='tabs'>
		<li class='active'><a href='#details' data-toggle='tab'>Details</a></li>
		<li><a href='#taxes' data-toggle='tab'>Tax</a></li>
		<li><a href='#images' data-toggle='tab'>Images</a></li>
		<li><a href='#categories' data-toggle='tab'>Categories</a></li>
	</ul>

	<div id="my-tab-content" class="tab-content" style="padding-top:25px;">
		<div class='tab-pane active' id='details'>	
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">SKU:</label>
					<div class="col-xs-12 col-sm-2">
						<input type="text" class="form-control" id='prod_sku' name="prod_sku" value=''>
				 	</div>
					<label class="control-label col-xs-1">Title:</label>
					<div class="col-xs-12 col-sm-6">
						<input type="text" class="form-control" id='prod_title' name="prod_title" value=''>
				 	</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Short Description:</label>
					<div class="col-xs-12 col-sm-10">
						<textarea class="form-control" id='prod_short_desc' name="prod_short_desc"></textarea>
				 	</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Long Description:</label>
					<div class="col-xs-12 col-sm-10">
						<textarea class="form-control" id='prod_long_desc' name="prod_long_desc"></textarea>
				 	</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Retail Cost:</label>
					<div class="col-xs-12 col-sm-2">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" class="form-control" id='prod_retail_cost' name="prod_retail_cost" value=''>
						</div>
				 	</div>
					<label class="control-label col-xs-12 col-sm-2">Base Cost:</label>
					<div class="col-xs-12 col-sm-2">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" class="form-control" id='prod_base_cost' name="prod_base_cost" value=''>
						</div>
					</div>
			 	</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Qty:</label>
					<div class="col-xs-12 col-sm-2">
						<input type="text" class="form-control" id='prod_qty' name="prod_qty" value=''>
				 	</div>
					<label class="control-label col-xs-12 col-sm-2">Reorder Qty:</label>
					<div class="col-xs-12 col-sm-2">
						<input type="text" class="form-control" id='prod_reorder_qty' name="prod_reorder_qty" value=''>
				 	</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Weight (grams):</label>
					<div class="col-xs-12 col-sm-2">
						<div class="input-group">
							<input type="text" class="form-control" id='prod_weight' name="prod_weight" value=''>
							<span class="input-group-addon">grams</span>
						</div>
				 	</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Combine Code:</label>
					<div class="col-xs-12 col-sm-2">
						<input type="text" class="form-control" id='prod_combine_code' name="prod_combine_code" value=''>
				 	</div>
				</div>
				<div class="form-group">
					<label class='control-label col-xs-12 col-sm-2'>Visible:</label>
					<div class='col-xs-8'>
						<div class="input-group">
							<input type='radio' name='prod_visible' value='Y' checked> YES &nbsp;&nbsp;
							<input type='radio' name='prod_visible' value='N'> NO
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class='control-label col-xs-12 col-sm-2'>Free Shipping:</label>
					<div class='col-xs-8'>
						<div class="input-group">
							<input type='radio' name='prod_has_free_shipping' value='Y'> YES &nbsp;&nbsp;
							<input type='radio' name='prod_has_free_shipping' value='N' checked> NO
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Valid From:</label>
					<div class="col-xs-3">
						<div class='input-group date' id='datetimepicker1'>
							<input type='text' class="form-control"  name='prod_date_valid_from' value='0000-00-00'>
							<span class="input-group-addon">
								<span class="fa fa-calendar"></span>
							</span>
						</div>
					</div>
					<label class="control-label col-xs-12 col-sm-2">Valid To:</label>
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
		</div>


		<div class='tab-pane' id='taxes'>	
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Tax Applicable:</label>
					<div class="col-xs-10 checkbox">
						<label> <input type="radio" name="prod_is_taxable" value="Y"> Product is Taxable </label><br>
						<label> <input type="radio" name="prod_is_taxable" value="N" checked> Product is Tax free </label><br>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Tax Rate:</label>
					<div class="col-xs-3">
						<div class='input-group'>
							<input type='text' class="form-control"  name='prod_tax_rate' >
							<span class="input-group-addon">%</span>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class='tab-pane' id='categories'>	
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Categories:</label>
					<div class="col-xs-10 checkbox">
					@foreach($categories as $c)
						<label> <input type="checkbox" name="categories[]" value="{{ $c->id }}"> {{$c->category_title }} </label><br>
					@endforeach
					</div>
				</div>
			</div>
		</div>

		<div class='tab-pane' id='images'>	
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Main Product Image:</label>
					<div class="col-xs-10">
						<input name="file" type="file" id="file">
						<button id='btnclear' type="button" class="btn btn-warning">Clear Image Selection</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2"> </label>
				<div class="col-xs-12">
					<button id='btnsave'   type="button" class="btn btn-success">Add Product</button>
					<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
			 	</div>
			</div>
		</div>
		<input type="hidden" name="prod_type" value="{{$product_type->id}}">
		{!! Form::token() !!}
		{!! Form::close() !!}
	</div>
</div>


<script>
$(function() { $('#datetimepicker1').datetimepicker({ format: 'YYYY-MM-DD', showTodayButton: true }); });
$(function() { $('#datetimepicker2').datetimepicker({ format: 'YYYY-MM-DD', showTodayButton: true }); });
</script>

<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>

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



$(function(){$(document).on('change',':file',function()
{
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [numFiles, label]);
	});
});
$("#file").on('fileselect', function(event, numFiles, label)
{
	var input = $(this).parents('.input-group').find(':text'),
		log = numFiles > 1 ? numFiles + ' files selected' : label;
	if(input.length) { input.val(log); } else { if( log ) alert(log); }
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
