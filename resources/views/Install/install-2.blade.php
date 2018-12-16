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
					<li role="presentation" class="active">
						<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Store Details">
							<span class="round-tab"> <i class="glyphicon glyphicon-home"></i> </span>
						</a>
					</li>
					<li role="presentation" class="disabled">
						<a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Contact Details">
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
	<form class='form-horizontal' name='install' id='install' method='post' enctype='multipart/form-data' style="padding:20px;">
		<div class="row">
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store Name:</label>
				<div class="col-xs-12 col-sm-3">
					<input type="text" class="form-control" id='store_name' name="store_name" value="Demo Store">
					Enter the full store name.
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Store ENV code:</label>
				<div class="col-xs-12 col-sm-4">
					<input type="text" class="form-control" id='store_env_code' name="store_env_code" value="DS"><br>
					ENV code is used for single and multi-store operating mode (suggestion, use capitals).
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Default Country:</label>
				<div class="col-xs-12 col-sm-4">
					<select class="form-control" id="store_iso_code" name="store_iso_code">
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
        </div>
		<div class="row">
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2"> </label>
				<div class="col-xs-12 col-sm-6">
					<button id='btnnext'  type="button" class="btn btn-success">Next</button>
				</div>
			</div>
		</div>
	<input type="hidden" name="key_hash" id="key_hash" value="{{$key_hash}}">
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>
<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>
<script>
$('#install').validate( 
{
	rules:
	{
		store_name: { required: true, minlength: 10 },
		store_env_code: { required: true, minlength: 2 }
	}
});
$('#btnnext').click( function() { $('#install').attr('action','/install/save/2'); $('#install').submit(); });
$('#btnprev').click( function() { $('#install').attr('action','/install/prev/1'); $('#install').submit(); });
</script>
@endsection
