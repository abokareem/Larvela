@extends('Install.install-template')
@section('header-step')
<div class="row">
	<section>
		<div class="wizard">
			<div class="wizard-inner">
				<div class="connecting-line"></div>
				<ul class="nav nav-tabs" role="tablis">
					<li role="presentation" class="disabled">
						<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Admin Details">
							<span class="round-tab"> <i class="glyphicon glyphicon-user"></i> </span>
						</a>
					</li>
					<li role="presentation" class="disabled">
						<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Store Details">
							<span class="round-tab"> <i class="glyphicon glyphicon-home"></i> </span>
						</a>
					</li>
					<li role="presentation" class="active">
						<a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Store Contact Details">
							<span class="round-tab"> <i class="glyphicon glyphicon-list"></i> </span>
						</a>
					</li>
					<li role="presentation" class="disabled">
						<a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
							<span class="round-tab"> <i class="glyphicon glyphicon-ok"></i> </span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</section>
</div>
@endsection 
@section('content')
<div class="container">
	<form class='form-horizontal' name='install' id='install' method='post' style="padding:20px;">
		<div class="row">
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store Name:</label>
				<div class="col-xs-12 col-sm-3">
					<strong>{{ $store->store_name }}</strong>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store URL::</label>
				<div class="col-xs-12 col-sm-4">
					<input type="text" class="form-control" id='store_url' name="store_url" value="" placeholder="https://your-site-here.xyz">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store Hours:</label>
				<div class="col-xs-12 col-sm-4">
					<input type="text" class="form-control" id='store_hours' name="store_hours" value="M-F 9-5">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store Sales Email:</label>
				<div class="col-xs-12 col-sm-5">
					<input type="text" class="form-control" id='store_sales_email' name="store_sales_email" value="">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Contact Number:</label>
				<div class="col-xs-12 col-sm-4">
					<input type="text" class="form-control" id='store_contact' name="store_contact" value="">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Address:</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" id='store_address' name="store_address" value="" placeholder="your street address">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Address 2:</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" id='store_address2' name="store_address2" value="" placeholder="suburb, postcode and state">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-4">Default Currency:</label>
				<div class="col-xs-12 col-sm-4">
					<select class="form-control" id="store_currency" name="store_currency">
					@foreach($currency as $c)
						<option value="{{$c->currency_code}}">{{$c->currency_name}}</option>
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
</script>
@endsection
