@extends('Templates.admin-master')
@section('title','Add Virtual Product')
@section('content')

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>


<div class='container'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Add Virtual (Limited) Product</h3></div>
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
		<li><a href='#actions' data-toggle='tab'>Actions</a></li>
	</ul>
	<div id="my-tab-content" class="tab-content" style="padding-top:25px;">
		<div class='tab-pane active' id='details'>
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">SKU:</label>
					<div class="col-xs-12 col-sm-2">
						<input type="text" class="form-control" id='prod_sku' name="prod_sku" value=''>
				 	</div>
					<label class="control-label col-xs-12 col-sm-2">Title:</label>
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
					<div class="col-xs-12 col-sm-3">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" class="form-control" id='prod_retail_cost' name="prod_retail_cost" value=''>
						</div>
				 	</div>
					<label class="control-label col-xs-12 col-sm-2">Base Cost:</label>
					<div class="col-xs-12 col-sm-3">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" class="form-control" id='prod_base_cost' name="prod_base_cost" value=''>
						</div>
				 	</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Qty Available:</label>
					<div class="col-xs-12 col-sm-3">
						<input type="text" class="form-control" id='prod_qty' name="prod_qty" value=''>
				 	</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Download<br>Content:</label>
					<div class="col-xs-8">
						<input type="radio" name="prod_weight" value='1' checked> Yes &nbsp;&nbsp;
						<input type="radio" name="prod_weight" value='0'> NO
				 	</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">Contect Path:</label>
					<div class="col-xs-10">
						<input type="text" class="form-control" id='prod_combine_code' name="prod_combine_code" value='/downloadable' placeholder="Content is stored in public but with a temporary GUID">
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
					<div class="col-xs-12 col-sm-4">
						<div class='input-group date' id='datetimepicker1'>
							<input type='text' class="form-control"  name='prod_date_valid_from' value='0000-00-00'>
							<span class="input-group-addon">
								<span class="fa fa-calendar"></span>
							</span>
						</div>
					</div>
					<label class="control-label col-xs-12 col-sm-2">Valid To:</label>
					<div class="col-xs-12 col-sm-4">
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
		<div class='tab-pane' id='actions'>
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">On Save Actions:</label>
					<div class="col-xs-12 col-sm-10">
						<div class="col-xs-12 col-sm-4">Available Actions:
							<select id="saveactions" class="form-control" size="5">
							@foreach($attributes as $attrib)
								<option value="{{ $attrib->id }}"> {{$attrib->attribute_name }} </option>
							@endforeach
							</select>
						</div>
						<div class="col-xs-12 col-sm-2 text-center">
							<button id="btnaddsa" type="button" class="btn btn-success"> &gt; </button><br><br>
							<button id="btnremovesa" type="button" class="btn btn-success"> &lt; </button>
						</div>
						<div class="col-xs-12 col-sm-4">Actions to apply:
							<select id="saveactions" name="saveactions[]" multiple class="form-control" size="5">
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-2">After Purchase Actions:</label>
					<div class="col-xs-12 col-sm-10">
						<div class="col-xs-12 col-sm-4">Available Actions:
							<select id="afteractions" class="form-control" size="5">
							@foreach($attributes as $attrib)
								<option value="{{ $attrib->id }}"> {{$attrib->attribute_name }} </option>
							@endforeach
							</select>
						</div>
						<div class="col-xs-12 col-sm-2 text-center">
							<button id="btnaddaa" type="button" class="btn btn-success"> &gt; </button><br><br>
							<button id="btnremoveaa" type="button" class="btn btn-success"> &lt; </button>
						</div>
						<div class="col-xs-12 col-sm-4">After Sale:
							<select id="afteractions" name="afteractions[]" multiple class="form-control" size="5">
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Add Product</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	<input type="hidden" name="prod_type" value="{{ $product_type->id }}">
	<input type="hidden" name="prod_reorder_qty" value='0000'>
	{!! Form::token() !!}
	{!! Form::close() !!}
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
