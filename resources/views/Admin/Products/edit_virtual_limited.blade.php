@extends("Templates.admin-master")
@section("title","Edit Virtual Product")
@section("content")

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>



<?php
function IsChecked($id, $items)
{
	foreach($items as $item)
	{
#		if($item->category_id == $id) return " checked ";
	}
	return "";
}
?>

<div class="container-fluid">

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
	<form class="form-horizontal" name="edit" id="edit" method="post" enctype="multipart/form-data">

	<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
		<li class="active"><a href="#details" data-toggle="tab">Details</a></li>
		<li><a href="#images" data-toggle="tab">Images</a></li>
		<li><a href="#categories" data-toggle="tab">Categories</a></li>
		<li><a href="#actions" data-toggle="tab">Actions</a></li>
	</ul>
	<div id="my-tab-content" class="tab-content" style="padding-top:25px;">
		<div class="tab-pane active" id="details">

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">SKU:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id="prod_sku" name="prod_sku" value="{{ $product->prod_sku }}">
		 	</div>
			<label class="control-label col-xs-1">Title:</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" id="prod_title" name="prod_title" value="{{ $product->prod_title }}">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Short Description:</label>
			<div class="col-xs-10">
				<input type="text" class="form-control" id="prod_short_desc" name="prod_short_desc" value="{{ $product->prod_short_desc }}">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Long Description:</label>
			<div class="col-xs-10">
				<textarea class="form-control" id="prod_long_desc" name="prod_long_desc" rows="12">{{ $product->prod_long_desc }}</textarea>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Retail Cost:</label>
			<div class="col-xs-2">
				<div class="input-group">
					<span class="input-group-addon">$</span>
					<input type="text" class="form-control" id="prod_retail_cost" name="prod_retail_cost" value="{{ $product->prod_retail_cost }}">
				</div>
		 	</div>
			<label class="control-label col-xs-2">Base Cost:</label>
			<div class="col-xs-2">
				<div class="input-group">
					<span class="input-group-addon">$</span>
					<input type="text" class="form-control" id="prod_base_cost" name="prod_base_cost" value="{{ $product->prod_base_cost }}">
				</div>
		 	</div>
		</div>

		<?php $token=""; ?>
		@foreach($product_types as $pt)
			@if($product->prod_type == $pt->id)
				<?php $token = $pt->product_type_token; ?>
			@endif
		@endforeach
		@if($token=="VLIMITED")
		<div class="form-group">
			<label class="control-label col-xs-2">Qty:</label>
			<div class="col-xs-2">
				<div class="input-group">
					<input type="text" class="form-control" id="prod_qty" name="prod_qty" value="{{ $product->prod_qty }}">
					<span class="input-group-addon">Maximum Available for sale</span>
				</div>
		 	</div>
		</div>
		@else
		<div class="form-group">
			<label class="control-label col-xs-2">Qty:</label>
			<div class="col-xs-2">
				<div class="input-group">
					<input type="text" class="form-control" id="prod_qty" name="prod_qty" value="{{ $product->prod_qty }}">
					<span class="input-group-addon">Sold so far</span>
				</div>
		 	</div>
		@endif
		<div class="form-group">
			<label class="control-label col-xs-2">Weight (grams):</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id="prod_weight" name="prod_weight" value="{{ $product->prod_weight }}">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Combine Code:</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" id="prod_combine_code" name="prod_combine_code" value="{{ $product->prod_combine_code }}">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Visible:</label>
			<div class="col-xs-8">
			<?php
			$ckyes="";
			$ckno="";
			if($product->prod_visible=="Y") $ckyes="checked";
			if($product->prod_visible=="N") $ckno="checked";
			?>
			<input type="radio" name="prod_visible" value="Y" {!! $ckyes !!}> YES<br>
			<input type="radio" name="prod_visible" value="N" {!! $ckno !!}> NO<br>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Free Shipping:</label>
			<div class="col-xs-8">
			<?php
			$ckfsyes="";
			$ckfsno="";
			if($product->prod_has_free_shipping=="1") $ckfsyes="checked";
			if($product->prod_has_free_shipping=="0") $ckfsno="checked";
			?>
			<input type="radio" name="prod_has_free_shipping" value="1" {!! $ckfsyes !!}> YES<br>
			<input type="radio" name="prod_has_free_shipping" value="0" {!! $ckfsno !!}> NO<br>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Upload File:</label>
			<div class="col-xs-12 col-sm-8">
				<h4>Upload (Downloadable) Content</h4>
				<div class="input-group">
					<label class="input-group-btn">
						<span class="btn btn-primary"> Browse&hellip; <input type="file" name="dfile[]" id="dfile" style="display: none;" multiple></span>
					</label>
					<input type="text" class="form-control" readonly>
				</div>
				<span class="help-block">Select files that will be downloaded at purchase time...</span>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Valid From:</label>
			<div class="col-xs-12 col-sm-3">
				<div class="input-group date" id="datetimepicker1">
					<input type="text" class="form-control"  name="prod_date_valid_from" value="{{ $product->prod_date_valid_from }}">
					<span class="input-group-addon">
						<span class="fa fa-calendar"></span>
					</span>
				</div>
			</div>
			<label class="control-label col-xs-2">Valid To:</label>
			<div class="col-xs-3">
				<div class="input-group date" id="datetimepicker2">
					<input type="text" class="form-control"  name="prod_date_valid_to" value="{{ $product->prod_date_valid_to }}">
					<span class="input-group-addon">
						<span class="fa fa-calendar"></span>
					</span>
				</div>
			</div>
		</div>
	</div>
	</div>


		<div class="tab-pane" id="categories">

			<?php
			$store_name = array(); 
			$store_name[0] = "Global Category";
			?>
			@foreach($stores as $s)
				<?php $store_name[$s->id] = $s->store_name; ?>
			@endforeach

			<?php $mapping = array(); ?>
			@foreach($catmappings as $cm)
				<?php array_push($mapping, $cm->category_id); ?>
			@endforeach
	
			<div class="row">
				<div class="control-group">
					<label class="control-label col-xs-2">Category:</label>
					<div class="col-xs-8">
					@foreach($categories as $cat)
						@if(in_array($cat->id, $mapping))
							<input type="checkbox" name="category[]" value="{{$cat->id}}" checked> {{ $cat->category_title }} &nbsp;&nbsp;<span style="color:blue; text-weight:bold;"><i> {{ $store_name[$cat->category_store_id] }}</i></span><br>
						@else
						<input type="checkbox" name="category[]" value="{{$cat->id}}"> {{ $cat->category_title }} &nbsp;&nbsp;<span style="color:blue; text-weight:bold;"><i> {{ $store_name[$cat->category_store_id] }}</i></span><br>
						@endif
					@endforeach
					</div>
				</div>
			</div>
		</div>


		<div class="tab-pane" id="images">
			<div class="row">
				<label class="control-label">Assigned Images:</label>
				@if(sizeof($images)>0)
				<table  class="table table-hover">
					<thead>
						<th>Order</th>
						<th>Name</th>
						<th>Folder</th>
						<th>Size</th>
						<th>HxW</th>
					</thead>
					<tbody>
					@foreach($images as $image)
						<tr>
							<td>{{ $image->image_order }}</td>
							<td>{{ $image->image_file_name }}</td>
							<td>{{ $image->image_folder_name }}</td>
							<td>{{ $image->image_size }} Bytes</td>
							<td>{{ $image->image_height }} x {{ $image->image_width }}</td>
							<td><i class="fa fa-trash"></i> <a href="/admin/image/delete/{{$image->id }}/{{ $product->id }}">Delete</a></td>
						</tr>
					@endforeach
					</tbody>
				</table>
				@else
					<strong>Warning!</strong> - No Images have been uploaded for this product.
				@endif
			</div>
			<div class="row">
				<div class="form-group">
					<label class="control-label col-xs-2">Upload Image:</label>
					<div class="col-xs-3">
						<input name="file" type="file" id="file">
						<label class="btn btn-default btn-file"></label>
					</div>
				</div>
			</div>
		</div>


		<div class="tab-pane" id="actions">
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
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id="btnsave"   type="button" class="btn btn-success">Save Product</button>
				<button id="btncancel" type="button" class="btn btn-warning">Cancel</button>
				<button id="btndelete" type="button" class="btn btn-danger">Delete</button>
		 	</div>
		</div>
	</div>
	<input type="hidden" name="id" value="{{ $product->id }}">
	<input type="hidden" id="prod_reorder_qty" name="prod_reorder_qty" value="0">
	<input type="hidden" id="prod_type" name="prod_type" value="{{ $product->prod_type }}">
	{!! Form::token() !!}
	{!! Form::close() !!}
	<br/>
	<br/>
	<br/>
	<br/>
</div>


<script>
$(function() { $("#datetimepicker1").datetimepicker({ format: "YYYY-MM-DD", showTodayButton: true }); });
$(function() { $("#datetimepicker2").datetimepicker({ format: "YYYY-MM-DD", showTodayButton: true }); });
</script>

<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js"></script>

<script>

$("#edit").validate(
{
	rules:
	{
		prod_sku: { required: true, minlength: 3 },
		prod_title: { required: true, minlength: 3 },
		prod_short_desc: { required: true, minlength: 7 },
		prod_long_desc: { required: true, minlength: 7 }
	}
});


$("#btnsave").click( function()
{
	$("#edit").attr("action","/admin/product/update/{{ $product->id }}");
	$("#edit").submit();
});


$("#btncancel").click(function()
{
	var url = "/admin/products";
	window.location.href = url;
});


$("#btndelete").click(function()
{
	$("#edit").attr("action","/admin/product/delete/{{ $product->id }}");
	$("#edit").submit();
});


$(function(){$(document).on('change',':file',function()
{
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [numFiles, label]);
	});
});
$("#dfile").on('fileselect', function(event, numFiles, label)
{
	var input = $(this).parents('.input-group').find(':text'),
		log = numFiles > 1 ? numFiles + ' files selected' : label;
	if(input.length) { input.val(log); } else { if( log ) alert(log); }
});




</script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>tinymce.init({ selector:"textarea" });</script>
<!-- Framework Version: {{ app()::VERSION }} -->
<!-- Theme Name: {{ $THEME_NAME }} -->
<!-- Theme Home: {{ $THEME_HOME }} -->
<!-- Store Code: {{ $store->store_env_code }} -->
@stop
