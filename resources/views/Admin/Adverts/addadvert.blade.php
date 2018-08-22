@extends('admin-master')
@section('title','Add New Advert')
@section('content')
<?php $today = date("Y-m-d"); ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header">Edit Advert</h3></div>
	</div>

	<form class='form-horizontal' name='add' id='add' method='post'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Name:</label>
			<div class="col-xs-5">
				<input type="text" class="form-control" id='advert_name' name="advert_name" value=''>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">HTML Code:</label>
			<div class="col-xs-8">
				<textarea class="form-control" id='advert_html_code' name="advert_html_code"></textarea>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Store Codes:</label>
			<div class="col-xs-2">
				<select class='form-control' id="store_parent_id" name="store_parent_id">
					<option value="0" selected>Global - All Stores</option>
					@foreach($stores as $store)
					<option value="{{ $store->id }}">{{ $store->store_name }}</option>
					@endforeach
				</select>
		 	</div>
		</div>
		<div class="control-group">
			<label class='control-label col-xs-2'>Visible:</label>
			<div class='col-xs-8'>
			<input type='radio' name='advert_status' value='A' checked> YES<br>
			<input type='radio' name='advert_status' value='N'> NO<br>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Valid From:</label>
			<div class="col-xs-3">
				<div class='input-group date' id='datetimepicker1'>
					<input type='text' class="form-control"  name='advert_date_from' value='{{ $today }}'>
					<span class="input-group-addon">
						<span class="fa fa-calendar"></span>
					</span>
				</div>
			</div>
			<label class="control-label col-xs-2">Valid To:</label>
			<div class="col-xs-3">
				<div class='input-group date' id='datetimepicker2'>
					<input type='text' class="form-control"  name='advert_date_to' value=''>
					<span class="input-group-addon">
						<span class="fa fa-calendar"></span>
					</span>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Save Advert</button>
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

<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>

<script>

$('#add').validate(
{
	rules:
	{
		advert_name: { required: true, minlength: 3 },
		advert_html_code: { required: true, minlength: 3 }
	}
});


$('#btnsave').click( function()
{
	$('#add').attr('action','/admin/advert/save');
	$('#add').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/adverts';
	window.location.href = url;
});
</script>
@stop
