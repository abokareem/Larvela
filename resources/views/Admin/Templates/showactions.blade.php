@extends('admin-master')
@section('title','System Actions')
@section('content')

	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-envelope"></i> System Actions</h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>ID</th>
				<th>System Process Name</th>
				<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach($actions as $a)
				<tr>
				<td>{{ $a->id }}</td>
				<td>{{ $a->action_name }}</td>
				<td>
					<a href="/admin/action/edit/{{ $a->id }}"><i class="fa fa-edit"></i> Edit</a>
					<a href="/admin/action/delete/{{ $a->id }}"><i class="fa fa-trash"></i> Delete</a>
				</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>


	<div class="row">
		<div class='col-xs-2'>&nbsp;</div>
		<div class="col-xs-8">
			<button type="button" id="addnew" name="addnew" class="btn btn-success">
				<i class="fa fa-user-plus"></i> Add Process
			</button>
		</div>
	</div>


<script>
$('#addnew').click(function()
{
var url = '/admin/action/add/';
window.location.href = url;
});
</script>
<!-- Admin.templates.showtemplate -->
@stop
