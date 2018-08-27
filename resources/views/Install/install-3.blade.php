@extends('Install.install-template')
@section('content')
<div class="container">
	<form class='form-horizontal' name='install' id='install' method='post' enctype='multipart/form-data' style="padding:20px;">
		<div class="row">
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2"> </label>
				<div class="col-xs-12 col-sm-8 progress">
						<div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store Name:</label>
				<div class="col-xs-12 col-sm-3">
					<strong>{{ $store->store_name }}</strong>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store URL::</label>
				<div class="col-xs-12 col-sm-4">
					<input type="text" class="form-control" id='store_url' name="store_url" value=""><br>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store Hours:</label>
				<div class="col-xs-12 col-sm-4">
					<input type="text" class="form-control" id='store_hours' name="store_hours" value="M-F 9-5"><br>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store Sales Email:</label>
				<div class="col-xs-12 col-sm-5">
					<input type="text" class="form-control" id='store_sales_email' name="store_sales_email" value=""><br>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Contact Number:</label>
				<div class="col-xs-12 col-sm-4">
					<input type="text" class="form-control" id='store_contact' name="store_contact" value=""><br>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Default Currency:</label>
				<div class="col-xs-12 col-sm-2">
					<select class="form-control" id="store_currency" name="store_currency">
					@foreach($currency as $c)
						<option value="{{$c}}">{{$c}}</option>
					@endforeach
					</select>
				</div>
			</div>
        </div>
		<div class="row">
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">&nbsp;</label>
				<div class="col-xs-12 col-sm-6">
					<button id='btnnext'  type="button" class="btn btn-success">Save</button>
					<button id='btnprev'  type="button" class="btn btn-danger">Previous</button>
				</div>
			</div>
		</div>
	<input type="hidden" name="key_hash" id="key_hash" value="{{$key_hash}}">
	<input type="hidden" name="id" id="id" value="{{$store->id}}">
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>
<script>
$('#btnnext').click( function() { $('#install').attr('action','/install/save/3'); $('#install').submit(); });
$('#btnprev').click( function() { $('#install').attr('action','/install/prev/2'); $('#install').submit(); });
</script>
@endsection
