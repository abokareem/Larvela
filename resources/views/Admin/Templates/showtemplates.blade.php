@extends('admin-master')
@section('title','Template Management')
@section('content')

	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-envelope"></i> Templates</h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>Mapping</th>
				<th>File Name</th>
				<th>Size</th>
				<th>Date Modified</th>
				</tr>
			</thead>
			<tbody>
			@foreach($template_mappings as $t)
				<tr onclick='select({{ $p->id }})';>
				<td>{{ $t->action }}</td>
				<td>{{ $t->name }}</td>
				<td>{{ $t->size }}</td>
				<td>{{ $t->date_modified }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>


	<div class="row">
		<div class='col-xs-6'>
		</div>
	</div>


	<div class="row">
		<a href='/admin/product/addnew'><button class="btn btn-success">
			<i class="fa fa-user-plus"></i> Add Mapping </button></a>
	</div>
<script>
function select(id)
{
var url = '/admin/template/edit/'+id;
window.location.href = url;
}
</script>
@stop
