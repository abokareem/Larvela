@extends('admin-master')
@section('title','Edit SEO Block')
@section('content')
<link href="/css/bootstrap-switch.css" rel="stylesheet">

<div class='container-fluid'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Edit Block</h3></div>
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


	<form class='form-horizontal' name='edit' id='edit' method='post'>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2">Token Name:</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" id='seo_token' name="seo_token" value='{{ $seoblock->seo_token }}'>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">HTML:</label>
			<div class="col-xs-8">
				<textarea rows="10" class="form-control" id='seo_html_data' name="seo_html_data">{{ $seoblock->seo_html_data }}</textarea>
		 	</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">HTML Editor:</label>
			<div class="col-xs-8">
				<?php $ckyes=''; if($seoblock->seo_edit=="Y") $ckyes='checked'; ?>
				<input class="bootstrap-switch" id='cb_editor' data-on-text="Enabled" type="checkbox" name="cb_editor" {!! $ckyes !!}>
		 	</div>
		</div>
		<div class="form-group">
			<label class='control-label col-xs-2'>Assign to Store:</label>
			<div class='col-xs-5'>
				{!! $store_select_list !!}
			</div>
		</div>
		<div class="form-group">
			<label class='control-label col-xs-2'>Text Block is:</label>
			<div class='col-xs-4'>
				<?php $ckyes=''; if($seoblock->seo_status=="A") $ckyes='checked'; ?>
				<input class="bootstrap-switch" id="cb_status" data-on-text="Active" data-off-color="danger" data-on-color="success" type="checkbox" name='cb_status' {!! $ckyes !!}>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btnsave'   type="button" class="btn btn-success">Save SEO Block</button>
				<button id='btncancel' type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	<input type='hidden' name='id' value='{{ $seoblock->id }}'>
	<input type='hidden' name='seo_status' value='A'>
	<input type='hidden' name='seo_edit' value='Y'>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>


<script src='//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js'></script>
<script src='/bootstrap-switch.js'></script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>

@if($seoblock->seo_edit=="Y")
<script>tinymce.init({ selector:'textarea' });</script>
@endif


<script>
$('#cb_editor').bootstrapSwitch();
$('#cb_status').bootstrapSwitch();

$('input[name="cb_status"]').on('switchChange.bootstrapSwitch', function(event, state)
{
	var v = (state==true) ? 'A' : 'N';
	console.log("S="+state+"  V="+v);
	$("input[name='seo_status']").val(v);
});


$('input[name="cb_editor"]').on('switchChange.bootstrapSwitch', function(event, state)
{
	var v = (state==true) ? 'Y' : 'N';
	$("input[name='seo_edit']").val(v);
	if(state==false)
	{
		tinymce.remove('textarea');
	}
	else
	{
		tinymce.init({ selector:'textarea' });
	} 
});


$('#edit').validate(
{
	rules:
	{
		seo_token: { required: true, minlength: 4 }
	}
});


$('#btnsave').click( function()
{
	$('#edit').attr('action','/admin/seo/update/{{ $seoblock->id }}');
	$('#edit').submit();
});


$('#btncancel').click(function()
{
	var url = '/admin/seo';
	window.location.href = url;
});
</script>
@stop
