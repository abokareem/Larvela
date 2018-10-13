@extends('Templates.admin-master')
@section('title','Edit Store')
@section('content')

<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header">Edit Store</h3></div>
	</div>

	<form class='form-horizontal' name='edit' id='edit' method='post'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Store Code::</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" class="form-control" id='store_env_code' name="store_env_code" value='{{ $store->store_env_code }}' style='text-transform:uppercase'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Store Name:</label>
			<div class="col-xs-12 col-sm-8">
				<input type="text" class="form-control" id='store_name' name="store_name" value='{{ $store->store_name }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">URL:</label>
			<div class="col-xs-12 col-sm-8">
				<input type="text" class="form-control" name="store_url" id='store_url' value="{{ $store->store_url }}" style='text-transform:lowercase'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Hours:</label>
			<div class="col-xs-12 col-sm-4">
				<input type="text" class="form-control" id='store_hours' name="store_hours" value='{{ $store->store_hours }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Contact Number:</label>
			<div class="col-xs-12 col-sm-4">
				<input type="text" class="form-control" id='store_contact' name="store_contact" value='{{ $store->store_contact }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Currency:</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" class="form-control" id='store_currency' name="store_currency" value='{{ $store->store_currency }}' style='text-transform:uppercase'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Sales email:</label>
			<div class="col-xs-12 col-sm-5">
				<input type="text" class="form-control" id='store_sales_email' name="store_sales_email" value='{{ $store->store_sales_email }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Main Store Logo (Filename):</label>
			<div class="col-xs-12 col-sm-5">
				<input type="text" class="form-control" id='store_logo_filename' name="store_logo_filename" value='{{ $store->store_logo_filename }}'/>
		 	</div>
			<div class="col-xs-12 col-sm-8"><label class="control-label">Alt Text:</label>
				<input type="text" class="form-control" id='store_logo_alt_text' name="store_logo_alt_text" value='{{ $store->store_logo_alt_text }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Logo Thumbnail:</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" class="form-control" id='store_logo_thumb' name="store_logo_thumb" value='{{ $store->store_logo_thumb }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Logo for Invoices:</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" class="form-control" id='store_logo_invoice' name="store_logo_invoice" value='{{ $store->store_logo_invoice }}'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Logo for Emails:</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" class="form-control" id='store_logo_email' name="store_logo_email" value='{{ $store->store_logo_email }}'/>
		 	</div>
		</div>
		<div class="control-group">
			<label class='control-label col-xs-12 col-sm-2'>Status</label>
			<div class='col-xs-12 col-sm-8'>
			<?php $status = $store->store_status; ?>
			<input type='radio' name='store_status' value='A' <?php echo ($status=='A')?"checked":""; ?> > Enabled<br>
			<input type='radio' name='store_status' value='C' <?php echo ($status=='C')?"checked":""; ?> > Disabled<br>
			</div>
		</div>
		<input type="hidden" name="id" value="{{ $store->id }}">
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2"> </label>
			<div class="col-xs-12 col-sm-10">
				<button id='btnsave'   type="button" class="btn btn-success">Save</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
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
		store_env_code: { required: true, minlength: 2 },
		store_name: { required: true, minlength: 3 },
		store_url: { required: true, minlength: 7 },
		store_hours: { required: true, minlength: 4 },
		store_currency: { required: true, minlength: 3 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/store/update/{{ $store->id }}');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/stores';
	window.location.href = url;
});
</script>
@stop
