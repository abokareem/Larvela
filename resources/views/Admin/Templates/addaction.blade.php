@extends('Templates.admin-master')
@section('title','Add System Actions')
@section('content')

<div class="container">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-plus"></i> Add System Action</h3></div>
	</div>

	<form name="edit" id="edit" method="post" class="form-horizontal">
	<div class="row">
		<div class="form-group">
			<label class="col-xs-2">Actions Name:</label>
			<div class="col-xs-8">
				<input type="text" class="form-control" name="action_name" id="action_name" value="">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-2">&nbsp;</label>
			<div class="col-xs-8">
				<button type="button" id="savebtn" name="savebtn" class="btn btn-success">
					<i class="fa fa-user-plus"></i> Save
				</button>
			</div>
		</div>
	
	</div>
	{!! Form::token() !!}
	</form>
</div>




<script>
$('#savebtn').click(function()
{
	$('#edit').attr('action','/admin/action/save');
	$('#edit').submit();

});
</script>
<!-- Admin.templates.showtemplate -->
@stop
