@extends('Templates.admin-master')
@section('name','Add New Store')
@section('content')

<div class='container'>
	<div class="row">
			<div class="col-lg-12"><h3 class="page-header">Add New Store</h3></div>
	</div>

	<form class='form-horizontal' name='edit' id='edit' method='post'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Environment Code:</label>
			<div class="col-xs-12 col-sm-2">
				<input type="text" class="form-control" name="store_env_code" id='store_anv_code' placeholder="2 or 3 chars" style='text-transform:uppercase'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Store Name:</label>
			<div class="col-xs-9">
				<input type="text" class="form-control" id='store_name' name="store_name" value=''>
		 	</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Address 1:</label>
			<div class="col-xs-12 col-sm-8">
				<input type="text" class="form-control" id='store_address' name="store_address" value='By Appointment Only'/>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Address 2:</label>
			<div class="col-xs-12 col-sm-8">
				<input type="text" class="form-control" id='store_address2' name="store_address2" value=''/>
		 	</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Country:</label>
			<div class="col-xs-12 col-sm-4">
			<select class="form-control" id="store_country_code" name="store_country_code">
			@foreach($countries as $c)
			@if($c->country_name == $tzdata)
				<option value="{{$c->iso_code}}" selected>{{$c->country_name}}</option>
			@else
				<option value="{{$c->iso_code}}">{{$c->country_name}}</option>
			@endif
			@endforeach
			</select>
		 	</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Currency:</label>
			<div class="col-xs-4">
				<select class="form-control" name="store_currency">
				<option value='AUD' selected>Australian Dollar</option>
				<option value='CAD'>Canadian Dollar</option>
				<option value='EUR'>Euro</option>
				<option value='GBP'>Great British Pound</option>
				<option value='JPY'>Japanese Yen</option>
				<option value='NZD'>New Zealand Dollar</option>
				<option value='RMB'>Renminbi</option>
				<option value='CNY'>Chinese Yuan</option>
				<option value='SGD'>Singapore dollar</option>
				<option value='USD'>United States Dollar</option>
				</select>
		 	</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">URL:</label>
			<div class="col-xs-7">
				<input type="text" class="form-control" id='store_url' name="store_url" value='' style='text-transform:lowercase'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Parent:</label>
			<div class="col-xs-4">
				<select class="form-control" id="store_parent_id" name="store_parent_id">
					<option value="0" selected>Global - All Stores</option>
					@foreach($stores as $s)
						<option value="{{$s->id}}">{{$s->store_name}}</option>
					@endforeach
				</select>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Store Hours:</label>
			<div class="col-xs-4">
				<input type="text" class="form-control" name="store_hours" id='store_hours' placeholder="9-f M-F 8-14 S,S">
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Logo File Name:</label>
			<div class="col-xs-4">
				<input type="text" class="form-control" name="store_logo_filename" id='store_logo_filename'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Logo Alt Text:</label>
			<div class="col-xs-7">
				<input type="text" class="form-control" name="store_logo_alt_text" id='store_logo_alt_text'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Logo Thumb:</label>
			<div class="col-xs-4">
				<input type="text" class="form-control" name="store_logo_thumb" id='store_logo_thumb'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Logo Invoice:</label>
			<div class="col-xs-4">
				<input type="text" class="form-control" name="store_logo_invoice" id='store_logo_invoice'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2">Logo Email:</label>
			<div class="col-xs-4">
				<input type="text" class="form-control" name="store_logo_email" id='store_logo_email'>
		 	</div>
		</div>
		<div class="control-group">
			<label class='control-label col-xs-12 col-sm-2'>Status</label>
			<div class='col-xs-8'>
			<input type='radio' name='store_status' value='A' checked> Enabled<br>
			<input type='radio' name='store_status' value='C'> Disabled<br>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Add Store</button>
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
		store_name: { required: true, minlength: 3 },
		store_env_code: { required: true, minlength: 2 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/store/save');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/stores';
	window.location.href = url;
});
</script>
@stop
