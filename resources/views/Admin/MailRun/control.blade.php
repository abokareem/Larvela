@extends("Templates.admin-master")
@section("name","Mail Run Control Panel")
@section("content")

<div class="container">

	<form class="form-horizontal" name="edit" id="edit" method="post">
	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"></label>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><strong>{{ $store->store_name }}</strong> </div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">Templates Available</label>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<select name="template" class="form-control">
					@foreach($templates as $t)
					<option value="{{$t}}">{{$t}}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"> </label>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<input type="checkbox" name="cb_customers" value="C"> Send to Customers - <i>( {{ $c_count }} )</i><br>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"> </label>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<input type="checkbox" name="cb_subscriptions" value="S"> Send to Subscribers - <i>( {{ $s_count }} )</i><br>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"> </label>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<input type="checkbox" name="cb_test" value="T" checked> Send a Test Run to Store Owner<br>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"> </label>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<button id="btnstart"  type="button" class="btn btn-success">Start Run</button>
				<button id="btncancel" type="button" class="btn btn-warning">Cancel</button>
		 	</div>
		</div>
	</div>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>


<script>

$("#btnstart").click( function()
{
	$("#edit").attr("action","/admin/mailrun/control");
	$("#edit").submit();
});


$("#btncancel").click(function()
{
	var url = "/dashboard";
	window.location.href = url;
});
</script>
@stop
