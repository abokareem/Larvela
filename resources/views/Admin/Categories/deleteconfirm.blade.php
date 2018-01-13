@extends("admin-master")
@section("title","Confirm Category Delete")
@section("content")

<link href="/css/bootstrap-switch.css" rel="stylesheet">
<script src="/bootstrap-switch.js"></script>

<div class="container">
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Confirm Category Delete</h3></div>
	</div>

	<form class="form-horizontal" id="dc" name="dc" method="post">
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Store:</label>
			<div class="col-xs-6"> {{ $stores[$category->category_store_id] }} </div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Title:</label>
			<div class="col-xs-6"> {{ $category->category_title }} </div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Description:</label>
			<div class="col-xs-6"> {{ $category->category_description }} </div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">URL:</label>
			<div class="col-xs-6"> {{ $category->category_url }} </div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Parent:</label>
			<div class="col-xs-5">
			@foreach($categories as $c)
				@if($c->id == $category->category_parent_id)
					{{ $c->category_title }} 
				@endif
			@endforeach
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Status:</label>
			<div class="col-xs-8">
			<?php $status = ""; if($category->category_status =="A") $status="checked"; ?>
			<input class="bootstrap-switch" id="cb_status" data-on-text="Enabled" data-off-text="Disabled" data-off-color="danger" data-on-color="success" type="checkbox" name="cb_status" {!! $status !!}>
			<input type="hidden" name="category_status" value="A">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-2">Visible:</label>
			<div class="col-xs-8">
			<?php $vis=""; if($category->category_visible == "Y") $vis="checked"; ?>
			<input class="bootstrap-switch" id="cb_vis" data-on-text="Visible" data-off-text="Hidden" data-off-color="danger" data-on-color="success" type="checkbox" name="cb_vis" {!! $vis !!}>
			<input type="hidden" name="category_visible" value="Y">
			</div>
		</div>

		<input type="hidden" name="id" value="{{ $category->id }}">
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id="btndelete" type="button" class="btn btn-danger">Delete</button>
				<button id="btncancel" type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	{!! Form::token() !!}
	</form>
</div>

<script>
$("#cb_status").bootstrapSwitch();
$('input[name="cb_status"]').on("switchChange.bootstrapSwitch", function(event, state)
{
	var v = (state==true) ? "Y" : "N";
	$('input[name="category_status"]').val(v);
});
$("#cb_vis").bootstrapSwitch();
$('input[name="cb_vis"]').on("switchChange.bootstrapSwitch", function(event, state)
{
	var v = (state==true) ? "Y" : "N";
	$('input[name="category_visible"]').val(v);
});

$("#btndelete").click( function()
{
	$("#dc").attr("action","/admin/category/deletecat/{{ $category->id }}");
	$("#dc").submit();
});


$("#btncancel").click(function()
{
	var url = "/admin/categories";
	window.location.href = url;
});

</script>
@stop
