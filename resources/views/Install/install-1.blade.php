@extends('Install.install-template')
@section('content')
<div class="container">
	<form class='form-horizontal' name='install' id='install' method='post' enctype='multipart/form-data' style="padding:20px;">
		<div class="row">
			<div class="form-group">
				<label class="control-label col-xs-2"> </label>
				<div class="col-xs-12 col-sm-8 progress">
						<div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">App Key:</label>
				<div class="col-xs-12 col-sm-3">
					<input type="text" class="form-control" id='app_key' name="app_key" value=''><br>
					Enter first 8 characters of APP_KEY from .env file after the <strong>"base64:"</strong>sub-string.
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Admin Full Name:</label>
				<div class="col-xs-12 col-sm-4">
					<input type="text" class="form-control" id='admin_name' name="admin_name" value=''>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Admin Email Address:</label>
				<div class="col-xs-12 col-sm-5">
					<input type="text" class="form-control" id='admin_email' name="admin_email" value=''>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">Admin Password:</label>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="input-group">
						<input type="text" class="form-control" id='admin_pwd' name="admin_pwd" value='' placeholder="8 characters, Letters and Numbers.">
						<span class="input-group-btn">
							<button type="button" id='generate' class="btn btn-success btn">Generate</button>
						</span>
					</div>
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
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>
<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>
<script>
$('#generate').click(function()
{
var text = "";
var possible = "{_}(-)[=]+;?>%$#@!^&*<ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
for( var i=0; i < 8; i++ )
text += possible.charAt(Math.floor(Math.random() * possible.length));
$("input[name=admin_pwd]").val( text );
});
<?php
/*
$('#install').validate( 
{
	rules:
	{
	    app_key: { required: true, minlength:8 },
		admin_name: { required: true, minlength: 3 },
		admin_email: { required: true, minlength: 5 },
		admin_pwd: { required: true, minlength: 8 }
	}
});
*/
?>
$('#btnnext').click( function() { $('#install').attr('action','/install/save/1'); $('#install').submit(); });
</script>
@endsection
